<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Resources\Pages\Page;

class ProjectTimeline extends Page
{
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.project-timeline';

    public ?Project $project; // Properti untuk menyimpan data proyek
    public array $tasksForTimeline = []; // Properti untuk menyimpan data tugas yang sudah diformat

    protected static ?string $title = 'Timeline Tugas Proyek'; // Judul halaman

    public function mount(int | string $record): void
    {
        $this->record = Project::findOrFail($record); // Dapatkan record proyek saat ini
        $this->project = $this->record; // Simpan ke properti project

        $this->prepareTasksForTimeline();
    }

    protected function prepareTasksForTimeline(): void
    {
        $tasks = $this->project->tasks() // Ambil tasks dari relasi di model Project
            ->whereNotNull('start_date') // Hanya tugas yang punya start_date
            ->whereNotNull('due_date')   // Dan due_date
            ->orderBy('start_date')
            ->get();

        foreach ($tasks as $task) {
            // Format data sesuai kebutuhan library JS (contoh untuk Frappe Gantt / Google Charts)
            $this->tasksForTimeline[] = [
                'id' => (string) $task->id, // ID harus string untuk beberapa library
                'name' => $task->title,
                'start' => $task->start_date->format('Y-m-d'), // Format YYYY-MM-DD
                'end' => $task->due_date->format('Y-m-d'),
                'progress' => $this->calculateTaskProgress($task), // Fungsi untuk menghitung progress (opsional)
                // 'dependencies' => '' // Jika ada dependensi antar tugas
                'custom_class' => strtolower(str_replace(' ', '-', $task->status)) // Class CSS berdasarkan status
            ];
        }
    }

    // Fungsi helper untuk menghitung progress (contoh sederhana)
    protected function calculateTaskProgress($task): int
    {
        if ($task->status === 'Selesai') {
            return 100;
        }
        // Logika progress lainnya bisa ditambahkan di sini
        return 30; // Default
    }

    // Anda bisa menambahkan properti lain yang dibutuhkan oleh view
    public function getHeading(): string
    {
        return $this->project?->name . ' - Timeline Tugas' ?? static::$title;
    }
}
