<x-app-layout>
    @slot('title', 'قائمة المنتجات والطلبيات')

    <div class="container mx-auto p-4 sm:p-6 space-y-8 font-sans" dir="rtl">

        {{-- الهيدر العلوي المطور --}}
        <div class="bg-white p-6 rounded-2xl shadow-xs border border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-800 flex items-center gap-2">🛒 منـصة طلـب المنتجـات</h1>
                <p class="text-xs text-gray-500 mt-1">تصفح الأقسام المتاحة لك، وحدد الكميات المطلوبة لتوليد فاتورتك المجمعة</p>
            </div>
            <a href="{{ route('customer.dashboard') }}" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-5 py-2.5 rounded-xl text-xs font-bold transition-all border border-gray-200 flex items-center gap-2 shadow-xs">
                ⬅️ العودة للوحة التحكم
            </a>
        </div>

        {{-- رسائل التنبيه الذكية --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-xs font-bold shadow-xs">
                🎉 {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-xs font-bold shadow-xs">
                ⚠️ {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-xs font-bold shadow-xs space-y-1">
                @foreach($errors->all() as $error)
                    <p>⚠️ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- الشاشة مقسمة لـ قسمين: اليمين للكروت واليسار للفواتير المجمعة --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            {{-- اليمين: عرض الفئات على هيئة كروت جذابة منظم جداً --}}
            <div class="lg:col-span-2 space-y-6">
                <form method="POST" action="{{ route('customer.orders.store') }}" id="orderForm">
                    @csrf

                    <div class="space-y-6">
                        @forelse($categories as $category)
                            {{-- كارت الفئة (Category Card) --}}
                            <div class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-3.5 flex justify-between items-center text-white">
                                    <h3 class="font-bold text-sm flex items-center gap-2">
                                        📂 قسم: {{ $category->name }}
                                    </h3>
                                    <span class="bg-white/20 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        {{ $category->products->count() }} منتج متاح
                                    </span>
                                </div>

                                {{-- قائمة منتجات الفئة --}}
                                <div class="p-4 divide-y divide-gray-100">
                                    @foreach($category->products as $product)
                                        <div class="py-3.5 flex items-center justify-between gap-4 hover:bg-gray-50/50 px-2 rounded-xl transition-all">
                                            <div class="space-y-1">
                                                <h4 class="font-bold text-gray-800 text-xs">{{ $product->name }}</h4>
                                                <div class="flex items-center gap-3 text-[10px] text-gray-400">
                                                    <span class="text-emerald-600 font-semibold">السعر: {{ number_format($product->price, 2) }} ج.م</span>
                                                    <span>•</span>
                                                    <span>المتاح بالمخزن: {{ $product->quantity }}</span>
                                                </div>
                                            </div>

                                            {{-- حقل اختيار الكمية المطور --}}
                                            <div class="flex items-center gap-2">
                                                <input type="hidden" name="items[{{ $product->id }}][product_id]" value="{{ $product->id }}">
                                                <span class="text-[11px] text-gray-500 font-medium">الكمية:</span>
                                                <input type="number" name="items[{{ $product->id }}][quantity]" value="0" min="0" max="{{ $product->quantity }}"
                                                       class="w-20 text-center border-gray-300 rounded-xl text-xs font-bold text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-1.5 shadow-xs">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="bg-white text-center py-12 rounded-2xl border border-gray-100 text-gray-400 text-sm">
                                🔒 لا توجد أقسام أو فئات مصرح لك باستعراضها حالياً.
                            </div>
                        @endforelse
                    </div>

                    @if($categories->count() > 0)
                        {{-- حقل الملاحظات الزكي --}}
                        <div class="bg-white p-5 rounded-2xl shadow-xs border border-gray-100 mt-6 space-y-2">
                            <label class="block text-xs font-bold text-gray-700">✍️ ملاحظات خاصة بالطلبية بالكامل:</label>
                            <textarea name="notes" rows="2" placeholder="اكتب أي تعليمات تريد إرسالها مع هذه الفاتورة المجمعة للأدمن..."
                                      class="w-full border-gray-200 rounded-xl text-xs text-gray-900 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all p-3"></textarea>
                        </div>

                        {{-- 🌟 الزرار الجذاب، الضخم والواضح جداً للطلب وتوليد الفاتورة 🌟 --}}
                        <div class="mt-6">
                            <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 text-white hover:from-emerald-600 hover:to-teal-700 font-black text-sm py-4 rounded-2xl transition-all shadow-md shadow-emerald-500/20 hover:shadow-lg hover:-translate-y-0.5 transform flex items-center justify-center gap-2">
                                🚀 عرض كل المنتجات المحددة وإرسال الطلبية كـ فاتورة كاملة
                            </button>
                        </div>
                    @endif
                </form>
            </div>

            {{-- اليسار: سجل فواتير العميل السابقة (كل طلبية كاملة في كارت مستقل تماماً) --}}
            <div class="space-y-4">
                <div class="bg-white p-4 rounded-2xl shadow-xs border border-gray-100">
                    <h3 class="font-black text-gray-700 text-xs border-b pb-3 mb-4 flex items-center gap-2">📋 أرشيف فواتيرك السابقة</h3>

                    <div class="space-y-4">
                        @forelse($orders as $order)
                            {{-- كارت فاتورة منفصلة بالكامل --}}
                            <div class="bg-gray-50/60 rounded-xl p-4 border border-gray-100 space-y-3">
                                <div class="flex justify-between items-center border-b pb-2 border-gray-200/50">
                                    <div class="space-y-0.5">
                                        <span class="font-mono text-[10px] text-gray-400 block">رقم الفاتورة</span>
                                        <span class="font-mono text-xs font-bold text-gray-700">{{ $order->order_number }}</span>
                                    </div>
                                    @if($order->status == 'pending')
                                        <span class="bg-amber-100/80 text-amber-800 px-2.5 py-0.5 rounded-full text-[10px] font-bold">⏳ معلقة</span>
                                    @elseif($order->status == 'approved')
                                        <span class="bg-green-100/80 text-green-800 px-2.5 py-0.5 rounded-full text-[10px] font-bold">✅ معتمدة</span>
                                    @else
                                        <span class="bg-red-100/80 text-red-800 px-2.5 py-0.5 rounded-full text-[10px] font-bold">❌ مرفوضة</span>
                                    @endif
                                </div>

                                {{-- قائمة المنتجات جوة الفاتورة دي بس --}}
                                <div class="text-[11px] text-gray-600 space-y-1">
                                    @foreach($order->items as $item)
                                        <div class="flex justify-between bg-white p-2 rounded-lg border border-gray-100">
                                            <span>📦 {{ $item->product->name ?? 'منتج محذوف' }}</span>
                                            <span class="font-bold text-gray-900">الكمية: <span class="text-blue-600">({{ $item->quantity }})</span></span>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- الإجمالي وزرار الطباعة المنفصل للفاتورة الحالية --}}
                                <div class="pt-2 border-t border-dashed border-gray-200 flex justify-between items-center">
                                    <a href="{{ route('customer.orders.print', $order->id) }}" target="_blank" class="bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg text-[10px] font-bold transition-colors flex items-center gap-1 shadow-xs">
                                        🖨️ طباعة هذه الفاتورة
                                    </a>
                                    <div class="text-left">
                                        <span class="text-[9px] text-gray-400 block">إجمالي الطلبية</span>
                                        <span class="font-mono text-xs font-black text-gray-900">{{ number_format($order->total_price, 2) }} ج.م</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400 text-xs">لا يوجد فواتير مسجلة باسمك حتى الآن.</div>
                        @endforelse
                    </div>

                    <div class="mt-4">{{ $orders->links() }}</div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
