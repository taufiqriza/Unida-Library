@php
    $pending = $record->total_items - $record->found_items - $record->missing_items;
    $progress = $record->total_items > 0 ? round((($record->found_items + $record->missing_items) / $record->total_items) * 100) : 0;
@endphp

<div style="margin-bottom: 1rem;">
    {{-- Stats Cards --}}
    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
        <div style="flex: 1; display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border-radius: 0.5rem; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#d97706" style="width: 1.25rem; height: 1.25rem;"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z" clip-rule="evenodd" /></svg>
            <div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #92400e; line-height: 1;" data-stat="pending">{{ $pending }}</div>
                <div style="font-size: 0.65rem; color: #b45309; text-transform: uppercase;">Pending</div>
            </div>
        </div>
        <div style="flex: 1; display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border-radius: 0.5rem; background: linear-gradient(135deg, #d1fae5 0%, #6ee7b7 100%);">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#059669" style="width: 1.25rem; height: 1.25rem;"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" /></svg>
            <div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #065f46; line-height: 1;" data-stat="found">{{ $record->found_items }}</div>
                <div style="font-size: 0.65rem; color: #047857; text-transform: uppercase;">Ditemukan</div>
            </div>
        </div>
        <div style="flex: 1; display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border-radius: 0.5rem; background: linear-gradient(135deg, #fee2e2 0%, #fca5a5 100%);">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#dc2626" style="width: 1.25rem; height: 1.25rem;"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" /></svg>
            <div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #991b1b; line-height: 1;" data-stat="missing">{{ $record->missing_items }}</div>
                <div style="font-size: 0.65rem; color: #b91c1c; text-transform: uppercase;">Hilang</div>
            </div>
        </div>
    </div>

    {{-- Progress Bar --}}
    <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0.75rem; background: #f3f4f6; border-radius: 0.5rem;">
        <span style="font-size: 0.75rem; color: #6b7280;">Progress</span>
        <div style="flex: 1; height: 0.5rem; background: #e5e7eb; border-radius: 9999px; overflow: hidden;">
            <div style="height: 100%; width: {{ $progress }}%; background: linear-gradient(90deg, #10b981, #34d399); border-radius: 9999px;"></div>
        </div>
        <span style="font-size: 0.875rem; font-weight: 700; color: #059669;">{{ $progress }}%</span>
    </div>

    {{-- Info --}}
    <div style="display: flex; justify-content: space-between; margin-top: 0.5rem; font-size: 0.75rem; color: #9ca3af;">
        <span>Total: <strong style="color: #6b7280;">{{ $record->total_items }}</strong> eksemplar</span>
        <span>{{ $record->branch?->name ?? 'Semua Cabang' }}</span>
    </div>
</div>
