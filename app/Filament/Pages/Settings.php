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
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Thông tin chung')
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
                                Forms\Components\Textarea::make('slogan')
                                    ->label('Slogan')
                                    ->required()
                                    ->rows(3),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Logo & Hình ảnh')
                            ->schema([
                                Forms\Components\FileUpload::make('logo')
                                    ->label('Logo thương hiệu')
                                    ->disk('public')
                                    ->directory('uploads/settings')
                                    ->deleteUploadedFileUsing(fn ($file) => Storage::disk('public')->delete($file))
                                    ->image()
                                    ->imageEditor(),
                                    
                                Forms\Components\FileUpload::make('size_shoes_image')
                                    ->label('Bảng size giày')
                                    ->disk('public')
                                    ->directory('uploads/settings')
                                    ->deleteUploadedFileUsing(fn ($file) => Storage::disk('public')->delete($file))
                                    ->image()
                                    ->imageEditor(),
                                  Forms\Components\FileUpload::make('og_img')
                                    ->label('Hình chia sẻ mạng xã hội (OG Image)')
                                    ->helperText('Hình ảnh hiển thị khi chia sẻ trang web lên mạng xã hội')
                                    ->disk('public')
                                    ->directory('uploads/settings')
                                    ->deleteUploadedFileUsing(fn ($file) => Storage::disk('public')->delete($file))
                                    ->image()
                                    ->imageEditor(),
                                    
                                Forms\Components\Textarea::make('seo_description')
                                    ->label('Mô tả SEO (hiển thị trong kết quả tìm kiếm)')
                                    ->rows(3)
                                    ->helperText('Nên có độ dài từ 50-160 ký tự để tối ưu hiển thị trên các công cụ tìm kiếm')
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Mạng xã hội')
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

                        Forms\Components\Tabs\Tab::make('Thông tin thanh toán')
                            ->schema([
                                Forms\Components\TextInput::make('bank_number')
                                    ->label('Số tài khoản'),
                                Forms\Components\TextInput::make('bank_account_name')
                                    ->label('Tên chủ tài khoản'),
                                Forms\Components\TextInput::make('bank_name')
                                    ->label('Tên ngân hàng'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Sản phẩm chứa chuỗi này sẽ bị ẩn')
                            ->schema([
                                Forms\Components\TextInput::make('ban_name_product_one')
                                    ->label('Chuỗi 1'),
                                Forms\Components\TextInput::make('ban_name_product_two')
                                    ->label('Chuỗi 2'),
                                Forms\Components\TextInput::make('ban_name_product_three')
                                    ->label('Chuỗi 3'),
                                Forms\Components\TextInput::make('ban_name_product_four')
                                    ->label('Chuỗi 4'),
                                Forms\Components\TextInput::make('ban_name_product_five')
                                    ->label('Chuỗi 5'),
                            ])->columns(2),
                            
                        Forms\Components\Tabs\Tab::make('Cấu hình giá')
                            ->schema([
                                Forms\Components\Select::make('dec_product_price_type')
                                    ->label('Kiểu giảm giá')
                                    ->options([
                                        'percent' => 'Theo phần trăm (%)',
                                        'price' => 'Theo giá tiền (VND)',
                                    ])
                                    ->default('percent')
                                    ->live()
                                    ->afterStateUpdated(fn ($state, callable $set) => 
                                        $state === 'percent' ? 
                                            $set('dec_product_price_label', 'Giảm giá sản phẩm (%)') : 
                                            $set('dec_product_price_label', 'Giảm giá sản phẩm (VND)')
                                    ),
                                Forms\Components\Hidden::make('dec_product_price_label')
                                    ->default('Giảm giá sản phẩm (%)'),
                                Forms\Components\TextInput::make('dec_product_price')
                                    ->label(fn (callable $get) => $get('dec_product_price_label') ?? 'Giảm giá sản phẩm (%)')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Select::make('round_price')
                                    ->label('Làm tròn giá')
                                    ->options([
                                        'up' => 'Làm tròn lên',
                                        'down' => 'Làm tròn xuống',
                                        'balance' => 'Làm tròn tiêu chuẩn',
                                    ])
                                    ->default('down'),
                                Forms\Components\Select::make('apply_price')
                                    ->label('Áp dụng giá')
                                    ->options([
                                        'apply' => 'Áp dụng',
                                        'not_apply' => 'Không áp dụng',
                                    ])
                                    ->default('not_apply'),
                            ])->columns(2),
                    ])
                    ->columnSpan('full'),
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