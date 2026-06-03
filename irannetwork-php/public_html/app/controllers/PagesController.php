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

    /** POST /contact — store message and redirect with flash. */
    public function submitContact(): void
    {
        if (!Csrf::verify($_POST[Csrf::name()] ?? null)) {
            flash('contact_error', 'توکن امنیتی نامعتبر است. لطفاً صفحه را تازه کنید.');
            redirect('/contact');
        }

        $name    = trim((string)($_POST['name'] ?? ''));
        $phone   = trim((string)($_POST['phone'] ?? ''));
        $email   = trim((string)($_POST['email'] ?? ''));
        $service = trim((string)($_POST['service'] ?? ''));
        $message = trim((string)($_POST['message'] ?? ''));

        $errors = [];
        if (mb_strlen($name) < 2)   $errors[] = 'نام را به‌درستی وارد کنید.';
        if (!preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) $errors[] = 'شماره تماس معتبر نیست.';
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'ایمیل معتبر نیست.';
        if (mb_strlen($message) < 10) $errors[] = 'متن پیام باید حداقل ۱۰ کاراکتر باشد.';

        if ($errors) {
            keep_old(compact('name','phone','email','service','message'));
            flash('contact_error', implode(' ', $errors));
            redirect('/contact');
        }

        try {
            if (Database::isConfigured()) {
                (new ContactMessage())->create([
                    'name'=>$name,'phone'=>$phone,
                    'email'=>$email !== '' ? $email : null,
                    'service'=>$service !== '' ? $service : null,
                    'message'=>$message,'status'=>'new',
                ]);
            }
            clear_old();
            flash('contact_success', 'پیام شما با موفقیت ارسال شد. به‌زودی با شما در ارتباط خواهیم بود.');
        } catch (Throwable $e) {
            flash('contact_error', 'ذخیره پیام ناموفق بود. لطفاً بعداً تلاش کنید.');
        }
        redirect('/contact');
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
