<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Member;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function barcode(Item $item)
    {
        return view('print.barcode', ['items' => collect([$item])]);
    }

    public function barcodes(Request $request)
    {
        $ids = explode(',', $request->get('ids', ''));
        $items = Item::with('book')->whereIn('id', $ids)->get();
        return view('print.barcode', ['items' => $items]);
    }

    public function label(Item $item)
    {
        return view('print.label', ['items' => collect([$item])]);
    }

    public function labels(Request $request)
    {
        $ids = explode(',', $request->get('ids', ''));
        $items = Item::with('book')->whereIn('id', $ids)->get();
        return view('print.label', ['items' => $items]);
    }

    public function memberCard(Member $member)
    {
        return view('print.member-card', ['members' => collect([$member])]);
    }

    public function memberCards(Request $request)
    {
        $ids = explode(',', $request->get('ids', ''));
        $members = Member::with('memberType')->whereIn('id', $ids)->get();
        return view('print.member-card', ['members' => $members]);
    }
}
