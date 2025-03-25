<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class Settings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament.pages.settings';
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationGroup = 'Cấu hình';
    protected static ?string $navigationLabel = 'Cài đặt hệ thống';
    protected static ?string $title = 'Cài đặt hệ thống';

    public ?array $data = [];
    public Setting $setting;

    public function mount(): void
    {
        $this->setting = Setting::firstOrCreate();
        $this->form->fill($this->setting->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin chung')
                    ->schema([
                        Forms\Components\TextInput::make('app_name')
                            ->label('Tên cửa hàng')
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required(),
                        Forms\Components\Textarea::make('address')
                            ->label('Địa chỉ')
                            ->required()
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Logo & Hình ảnh')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo thương hiệu')
                            ->disk('public')
                            ->directory('uploads/settings')
                            ->deleteUploadedFileUsing(fn ($file) => Storage::disk('public')->delete($file))
                            ->image()
                            ->imageEditor()
                    ])->columns(2),

                Forms\Components\Section::make('Mạng xã hội')
                    ->schema([
                        Forms\Components\TextInput::make('zalo')
                            ->label('Số Zalo'),
                        Forms\Components\TextInput::make('facebook')
                            ->label('Facebook URL')
                            ->url(),
                        Forms\Components\TextInput::make('messenger')
                            ->label('Messenger URL')
                            ->url(),
                        Forms\Components\TextInput::make('link_tiktok')
                            ->label('Tiktok URL')
                            ->url(),
                    ])->columns(2),

                Forms\Components\Section::make('Thông tin thanh toán')
                    ->schema([
                        Forms\Components\TextInput::make('bank_number')
                            ->label('Số tài khoản'),
                        Forms\Components\TextInput::make('bank_account_name')
                            ->label('Tên chủ tài khoản'),
                        Forms\Components\TextInput::make('bank_name')
                            ->label('Tên ngân hàng'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $this->setting->fill($data)->save();

        Notification::make()
            ->success()
            ->title('Lưu cài đặt thành công')
            ->send();
    }
}