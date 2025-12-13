<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Member;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function barcode(Item $item)
    {
        $item->load(['book.authors', 'collectionType', 'location']);
        return view('print.barcode', ['items' => collect([$item])]);
    }

    public function barcodes(Request $request)
    {
        // Support both 'ids' and 'items' parameter
        $ids = $this->parseIds($request->get('ids') ?? $request->get('items'));
        $items = Item::with(['book.authors', 'collectionType', 'location'])->whereIn('id', $ids)->get();
        return view('print.barcode', ['items' => $items]);
    }

    public function label(Item $item)
    {
        $item->load(['book.authors', 'collectionType', 'location']);
        return view('print.label', ['items' => collect([$item])]);
    }

    public function labels(Request $request)
    {
        $ids = $this->parseIds($request->get('ids') ?? $request->get('items'));
        $items = Item::with(['book.authors', 'collectionType', 'location'])->whereIn('id', $ids)->get();
        return view('print.label', ['items' => $items]);
    }

    public function memberCard(Member $member)
    {
        return view('print.member-card', ['members' => collect([$member])]);
    }

    public function memberCards(Request $request)
    {
        $ids = $this->parseIds($request->get('ids'));
        $members = Member::with('memberType')->whereIn('id', $ids)->get();
        return view('print.member-card', ['members' => $members]);
    }

    private function parseIds($ids): array
    {
        if (is_array($ids)) {
            return $ids;
        }
        if (is_string($ids) && !empty($ids)) {
            return explode(',', $ids);
        }
        return [];
    }
}
