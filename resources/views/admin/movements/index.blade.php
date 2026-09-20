@extends('layouts.admin')

@section('title', ' المراقبة والتقارير')

@section('content')
<div class="container mx-auto p-6 space-y-8">

    {{-- هيدر الصفحة الأصلي --}}
    <div class="bg-gradient-to-r border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">📊 إدارة حركات المخزن</h1>
        <p class="text-sm text-gray-500 mt-1">متابعة حية لسحبيات العمال واعتماد طلبيات فواتير العملاء - شركة <span class="font-bold text-blue-600">AdZone</span></p>
    </div>

    {{-- القسم الأول: أزرار فلاتر العمال --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-700 flex items-center gap-2">👷‍♂️ كشف سحبيات عمال خط الإنتاج</h3>

            {{-- أزرار الإجراءات الخاصة بالعامل المختار --}}
            @if($selectedWorkerId)
            <div class="flex gap-2">
                {{-- زرار طباعة التقرير الجديد --}}
                <a href="{{ route('admin.movements.worker-report', $selectedWorkerId) }}" target="_blank"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-700 transition-all">
                    🖨️ طباعة تقرير PDF
                </a>

                {{-- زرار تصفير السجلات --}}
                <form method="POST" action="{{ route('admin.movements.clear-logs', $selectedWorkerId) }}" onsubmit="return confirm('تنبيه: هل أنت متأكد من مسح جميع سجلات هذا العامل؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-red-600 transition-all">
                        مسح سجلات هذا العامل 🧹
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- أزرار الفلترة الأصلية --}}
        <div class="flex flex-wrap gap-2 mb-6">
            <a href="{{ route('admin.movements.index') }}"
               class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ !$selectedWorkerId ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                كل العمال 👥
            </a>
            @foreach($workers as $worker)
                <a href="{{ route('admin.movements.index', ['worker_id' => $worker->id]) }}"
                   class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $selectedWorkerId == $worker->id ? 'bg-purple-600 text-white' : 'bg-purple-50 text-purple-700 hover:bg-purple-100' }}">
                    👤 {{ $worker->name }}
                </a>
            @endforeach
        </div>

        {{-- جدول عرض حركات المخزن --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs border-b">
                        <th class="p-3">وقت السحب</th>
                        <th class="p-3">اسم العامل</th>
                        <th class="p-3">المنتج المسحوب</th>
                        <th class="p-3 text-center">الكمية المسحوبة</th>
                        <th class="p-3">ملاحظات التشغيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($workerLogs as $log)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-3 font-mono text-xs text-gray-400">{{ $log->created_at->format('Y-m-d h:i A') }}</td>
                        <td class="p-3 font-bold text-purple-700">{{ $log->user->name ?? 'عامل' }}</td>
                        <td class="p-3 font-semibold text-gray-800">{{ $log->product->name ?? 'منتج محذوف' }}</td>
                        <td class="p-3 text-center font-bold text-red-600">- {{ $log->quantity }} {{ $log->product->unit ?? '' }}</td>
                        <td class="p-3 text-gray-500 text-xs">{{ $log->notes ?? 'سحب مباشر لعملية التصنيع' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-gray-400">لا توجد حركات سحب مسجلة لهذا العامل حالياً.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $workerLogs->appends(['worker_id' => $selectedWorkerId])->links() }}</div>
    </div>

    {{-- القسم الثاني: فواتير وطلبات العملاء الاصلي --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-700 mb-4 flex items-center gap-2">🛒 طلبات المشتريات وفواتير العملاء</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs border-b">
                        <th class="p-3">رقم الفاتورة</th>
                        <th class="p-3">العميل</th>
                        <th class="p-3">المنتجات</th>
                        <th class="p-3">الإجمالي</th>
                        <th class="p-3 text-center">الحالة</th>
                        <th class="p-3 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-3 font-mono text-xs text-gray-500 font-bold">#{{ $order->order_number }}</td>
                        <td class="p-3 font-semibold text-gray-800">{{ $order->customer_name }}</td>
                        <td class="p-3 text-gray-600 text-xs">
                            @foreach($order->items as $item)
                                <div class="bg-gray-50 p-1 my-1 rounded border">📦 {{ $item->product->name ?? '...' }} <span class="text-blue-600 font-bold">({{$item->quantity}})</span></div>
                            @endforeach
                            @if($order->notes)
                                <div class="mt-1 text-[10px] bg-amber-50 text-amber-800 p-1.5 rounded border border-amber-200">✍️ <strong>ملاحظات:</strong> {{ $order->notes }}</div>
                            @endif
                        </td>
                        <td class="p-3 font-bold text-gray-900">{{ number_format($order->total_price, 2) }} ج.م</td>
                        <td class="p-3 text-center">
                            @if($order->status == 'pending') <span class="bg-amber-100 text-amber-800 px-2 py-1 rounded-full text-[10px] font-bold">⏳ معلق</span>
                            @elseif($order->status == 'approved') <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-[10px] font-bold">✅ تم الاعتماد</span>
                            @else <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-[10px] font-bold">❌ مرفوض</span> @endif
                        </td>
                        <td class="p-3 text-center space-y-1">
                            @if($order->status == 'pending')
                                <form method="POST" action="{{ route('admin.movements.order-approve', $order->id) }}">@csrf <button type="submit" class="w-full bg-green-600 text-white px-2 py-1 rounded text-[10px] font-bold hover:bg-green-700">موافقة</button></form>
                                <form method="POST" action="{{ route('admin.movements.order-reject', $order->id) }}">@csrf <button type="submit" class="w-full bg-red-600 text-white px-2 py-1 rounded text-[10px] font-bold hover:bg-red-700">رفض</button></form>
                            @endif
                            <a href="{{ route('admin.movements.print-invoice', $order->id) }}" target="_blank" class="block w-full bg-blue-500 text-white px-2 py-1 rounded text-[10px] font-bold hover:bg-blue-600 text-center mt-1">📥 تحميل PDF</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-6 text-gray-400">لا توجد طلبات حالياً.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
