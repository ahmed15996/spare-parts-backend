@extends('static-pages.layouts.main')

@section('title', 'سياسة الخصوصية | Auto Brokers')

@section('content')
    <main class="min-h-screen bg-slate-50 py-10 px-4">
        <div class="max-w-4xl mx-auto">
            <header class="mb-6 text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900">سياسة الخصوصية</h1>
                <p class="mt-3 text-sm md:text-base text-slate-600">
                    نلتزم بحماية بياناتك وخصوصيتك أثناء استخدام خدماتنا.
                </p>
            </header>

            <section class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 md:p-10">
                <article dir="rtl" class="text-right text-slate-800 leading-8 space-y-4">
                    {!! $privacyAr !!}
                </article>
            </section>
        </div>
    </main>
@endsection
