<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomepageSetting;
use App\Models\HomepageSlide;
use Illuminate\Support\Facades\Storage;

class AdminHomepageController extends Controller
{
    public function index()
    {
        $setting = HomepageSetting::first();
        if(!$setting){
            $setting = HomepageSetting::create([
                'hero_title' => 'Portal Karir Alumni',
                'hero_subtitle' => 'BKK SMKN 1 Surabaya',
            ]);
        }
        $slides = HomepageSlide::orderBy('sort_order')->get();
        return view('admin.homepage.index', compact('setting','slides'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'show_slider' => 'nullable|boolean',
            'show_hero_text' => 'nullable|boolean',
        ]);
        $setting = HomepageSetting::first() ?? new HomepageSetting();
        $setting->fill($data);
        $setting->show_slider = $request->boolean('show_slider');
        $setting->show_hero_text = $request->boolean('show_hero_text');
        $setting->save();
        return redirect()->route('admin.homepage.index')->with('success','Pengaturan homepage berhasil diperbarui');
    }

    public function storeSlide(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
            'image' => 'required|image|max:2048',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);
        if($request->hasFile('image')){
            $data['image'] = $request->file('image')->store('homepage_slides','public');
        }
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (HomepageSlide::max('sort_order') ?? 0) + 1;
        HomepageSlide::create($data);
        return redirect()->route('admin.homepage.index')->with('success','Slide berhasil ditambahkan');
    }

    public function updateSlide(Request $request, HomepageSlide $slide)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:2048',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|url',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);
        if($request->hasFile('image')){
            if($slide->image && Storage::disk('public')->exists($slide->image)){
                Storage::disk('public')->delete($slide->image);
            }
            $data['image'] = $request->file('image')->store('homepage_slides','public');
        }
        $data['is_active'] = $request->boolean('is_active');
        $slide->update($data);
        return redirect()->route('admin.homepage.index')->with('success','Slide berhasil diperbarui');
    }

    public function deleteSlide(HomepageSlide $slide)
    {
        if($slide->image && Storage::disk('public')->exists($slide->image)){
            Storage::disk('public')->delete($slide->image);
        }
        $slide->delete();
        return redirect()->route('admin.homepage.index')->with('success','Slide berhasil dihapus');
    }
}
