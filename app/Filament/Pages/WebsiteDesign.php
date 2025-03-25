<?php

namespace App\Filament\Pages;

use App\Models\WebsiteDesign as WebsiteDesignModel;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class WebsiteDesign extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament.pages.website-design';
    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?int $navigationSort = 9;
    protected static ?string $navigationGroup = 'Cấu hình';
    protected static ?string $navigationLabel = 'Giao diện Website';
    protected static ?string $title = 'Tùy chỉnh giao diện Website';

    public ?array $data = [];
    public WebsiteDesignModel $websiteDesign;

    public function mount(): void
    {
        $this->websiteDesign = WebsiteDesignModel::firstOrCreate();
        $this->form->fill($this->websiteDesign->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Stats Section
                Forms\Components\Section::make('Thống kê')
                    ->schema([
                        Forms\Components\Toggle::make('stat_status')
                            ->label('Hiển thị phần thống kê')
                            ->default(true),
                        Forms\Components\TextInput::make('stat_product')
                            ->label('Số liệu sản phẩm'),
                        Forms\Components\TextInput::make('stat_follower')
                            ->label('Số liệu follower'),
                        Forms\Components\TextInput::make('stat_eval')
                            ->label('Số liệu đánh giá'),
                    ])->columns(2),

                // Services Section
                Forms\Components\Section::make('Dịch vụ')
                    ->schema([
                        Forms\Components\Toggle::make('service_status')
                            ->label('Hiển thị phần dịch vụ')
                            ->default(true),
                        ...collect(range(1, 4))->map(fn ($i) => [
                            Forms\Components\FileUpload::make("service_pic_{$i}")
                                ->label("Ảnh dịch vụ {$i}")
                                ->image()
                                ->directory('uploads/services'),
                            Forms\Components\TextInput::make("service_title_{$i}")
                                ->label("Tiêu đề dịch vụ {$i}"),
                            Forms\Components\Textarea::make("service_des_{$i}")
                                ->label("Mô tả dịch vụ {$i}")
                                ->rows(3),
                        ])->flatten()->toArray(),
                    ])->columns(2),

                // Banner Images Section
                Forms\Components\Section::make('Banner')
                    ->schema([
                        Forms\Components\Toggle::make('image_banner_status')
                            ->label('Hiển thị phần banner')
                            ->default(true),
                        ...collect(range(1, 4))->map(fn ($i) => 
                            Forms\Components\FileUpload::make("image_banner_link{$i}")
                                ->label("Banner {$i}")
                                ->image()
                                ->directory('uploads/banners')
                        )->toArray(),
                    ])->columns(2),

                // Effects Section
                Forms\Components\Section::make('Công dụng sản phẩm')
                    ->schema([
                        Forms\Components\Toggle::make('effect_status')
                            ->label('Hiển thị phần công dụng')
                            ->default(true),
                        ...collect(range(1, 4))->map(fn ($i) => 
                            Forms\Components\FileUpload::make("effect_pic_{$i}")
                                ->label("Ảnh công dụng {$i}")
                                ->image()
                                ->directory('uploads/effects')
                        )->toArray(),
                    ])->columns(2),

                // Real Reviews Section
                Forms\Components\Section::make('Đánh giá thực')
                    ->schema([
                        Forms\Components\Toggle::make('rep_like_real_status')
                            ->label('Hiển thị phần đánh giá')
                            ->default(true),
                        Forms\Components\FileUpload::make('rep_like_real_pic')
                            ->label('Ảnh đánh giá')
                            ->image()
                            ->directory('uploads/reviews'),
                        Forms\Components\TextInput::make('rep_like_real_link')
                            ->label('Link đánh giá')
                            ->url(),
                    ])->columns(2),

                // Final Banner Section
                Forms\Components\Section::make('Banner cuối trang')
                    ->schema([
                        Forms\Components\Toggle::make('banner_final_status')
                            ->label('Hiển thị banner cuối')
                            ->default(true),
                        Forms\Components\FileUpload::make('banner_final_pic')
                            ->label('Ảnh banner')
                            ->image()
                            ->directory('uploads/final-banner'),
                    ])->columns(2),

                // Certificate Section
                Forms\Components\Section::make('Chứng nhận')
                    ->schema([
                        Forms\Components\Toggle::make('cer_status')
                            ->label('Hiển thị phần chứng nhận')
                            ->default(true),
                        Forms\Components\TextInput::make('cer_title')
                            ->label('Tiêu đề'),
                        Forms\Components\Textarea::make('cer_des')
                            ->label('Mô tả')
                            ->rows(3),
                        Forms\Components\TextInput::make('cer_link')
                            ->label('Link chứng nhận')
                            ->url(),
                        Forms\Components\FileUpload::make('cer_image')
                            ->label('Ảnh chứng nhận')
                            ->image()
                            ->directory('uploads/certificates'),
                    ])->columns(2),

                // Video Section
                Forms\Components\Section::make('Video')
                    ->schema([
                        Forms\Components\Toggle::make('video_status')
                            ->label('Hiển thị phần video')
                            ->default(true),
                        Forms\Components\TextInput::make('video_link')
                            ->label('Link video')
                            ->url(),
                    ])->columns(2),

                // About Section
                Forms\Components\Section::make('Giới thiệu')
                    ->schema([
                        Forms\Components\Toggle::make('about_status')
                            ->label('Hiển thị phần giới thiệu')
                            ->default(true),
                        Forms\Components\TextInput::make('about_title')
                            ->label('Tiêu đề'),
                        Forms\Components\FileUpload::make('about_pic')
                            ->label('Ảnh giới thiệu')
                            ->image()
                            ->directory('uploads/about'),
                        Forms\Components\Textarea::make('about_des')
                            ->label('Nội dung giới thiệu')
                            ->rows(3),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        // Xử lý xóa file cũ khi upload file mới
        $oldData = $this->websiteDesign->toArray();
        foreach ($data as $key => $value) {
            if (str_contains($key, '_pic') || str_contains($key, '_image') || str_contains($key, 'link') && is_string($oldData[$key])) {
                if ($oldData[$key] !== $value) {
                    Storage::disk('public')->delete($oldData[$key]);
                }
            }
        }

        $this->websiteDesign->fill($data)->save();

        Notification::make()
            ->success()
            ->title('Lưu cài đặt thành công')
            ->send();
    }
}