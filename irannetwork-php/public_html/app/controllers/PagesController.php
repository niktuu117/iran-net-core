<?php
declare(strict_types=1);

class PagesController extends Controller
{
    public function home(): void
    {
        $this->view('public/home', [
            'pageTitle'       => 'ایران نتورک | خدمات تخصصی شبکه، سرور و امنیت',
            'pageDescription' => 'ایران نتورک ارائه‌دهنده خدمات حرفه‌ای شبکه، پشتیبانی سرور، امنیت، ویپ و دیجیتال مارکتینگ برای کسب‌وکارها و سازمان‌ها در تهران و اصفهان.',
            'canonical'       => '/',
        ]);
    }

    public function about(): void
    {
        $this->view('public/about', [
            'pageTitle'       => 'درباره ایران نتورک | شرکت تخصصی خدمات شبکه و سرور',
            'pageDescription' => 'با ایران نتورک، تجربه، تخصص و تعهد در حوزه خدمات شبکه، سرور و امنیت سازمانی آشنا شوید.',
            'canonical'       => '/about',
        ]);
    }

    public function contact(): void
    {
        $this->view('public/contact', [
            'pageTitle'       => 'تماس با ایران نتورک | مشاوره و درخواست خدمات',
            'pageDescription' => 'برای دریافت مشاوره رایگان خدمات شبکه، سرور، امنیت و ویپ با کارشناسان ایران نتورک در تهران و اصفهان در ارتباط باشید.',
            'canonical'       => '/contact',
        ]);
    }

    public function faq(): void
    {
        $this->view('public/faq', [
            'pageTitle'       => 'سوالات متداول | ایران نتورک',
            'pageDescription' => 'پاسخ پرسش‌های پرتکرار درباره خدمات شبکه، پشتیبانی سرور، امنیت و قراردادهای ایران نتورک.',
            'canonical'       => '/faq',
        ]);
    }

    public function rules(): void
    {
        $this->view('public/rules', [
            'pageTitle'       => 'قوانین و مقررات | ایران نتورک',
            'pageDescription' => 'قوانین و مقررات استفاده از خدمات و وب‌سایت ایران نتورک.',
            'canonical'       => '/rules',
        ]);
    }

    public function blog(): void
    {
        $this->view('public/blog', [
            'pageTitle'       => 'مقالات و آموزش‌ها | ایران نتورک',
            'pageDescription' => 'مقالات تخصصی درباره شبکه، سرور، امنیت، ویپ و دیجیتال مارکتینگ از تیم ایران نتورک.',
            'canonical'       => '/blog',
        ]);
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view('public/404', [
            'pageTitle'       => 'صفحه یافت نشد | ایران نتورک',
            'pageDescription' => 'صفحه‌ای که به دنبال آن هستید پیدا نشد.',
            'canonical'       => '/404',
            'noindex'         => true,
        ]);
    }
}
