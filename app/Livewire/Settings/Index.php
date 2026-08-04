<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public string $applicationName = 'InvoicePro';
    public string $exportFolder = '';
    public string $theme = 'light';
    public string $language = 'en';
    public $databaseFile;

    public function mount(): void
    {
        $this->applicationName = Setting::valueFor('application_name', 'InvoicePro');
        $this->exportFolder = Setting::valueFor('export_folder', storage_path('app/exports'));
        $this->theme = Setting::valueFor('theme', 'light');
        $this->language = Setting::valueFor('language', 'en');
    }

    public function save(): void
    {
        $this->validate(['applicationName' => 'required|max:80', 'exportFolder' => 'required|max:500', 'theme' => 'required|in:light,dark,system', 'language' => 'required|in:en,ar']);
        foreach (['application_name' => $this->applicationName, 'export_folder' => $this->exportFolder, 'theme' => $this->theme, 'language' => $this->language] as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        app()->setLocale($this->language);
        session()->flash('message', __('Settings saved.'));
        $this->redirectRoute('settings');
    }

    public function backup()
    {
        $source = database_path('database.sqlite');
        $name = 'invoicepro-backup-'.now()->format('Y-m-d-His').'.sqlite';
        return response()->download($source, $name);
    }

    public function restore(): void
    {
        $this->validate(['databaseFile' => 'required|file|max:51200']);
        File::copy($this->databaseFile->getRealPath(), database_path('database.sqlite'));
        session()->flash('message', __('Database restored. Refresh the application.'));
    }

    public function render()
    {
        return view('livewire.settings.index')->layout('components.layouts.app', ['title' => __('Settings')]);
    }
}
