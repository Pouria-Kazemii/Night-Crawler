<?php

namespace App\Http\Controllers;

use App\Contracts\SubscribeManagerInterface;
use App\Http\Requests\CustomerFormRequest;
use App\Http\Requests\ResubscribeRequest;
use App\Models\Crawler;
use App\Models\Customer;
use App\Models\Subscribe;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomersController extends Controller
{
    public function index(): View
    {
        $customers = Customer::with('subscribe')->paginate(15);

        $subscribes = Subscribe::all();

        return view('customer.index', compact('customers', 'subscribes'));
    }

    public function create(): View
    {
        $subscribes = Subscribe::all();

        $crawlers = Crawler::all()->pluck('title', 'id')->toArray();

        return view('customer.create', compact('subscribes', 'crawlers'));
    }

    public function store(CustomerFormRequest $request,SubscribeManagerInterface $subscribe): RedirectResponse
    {
        $validated = $request->validated();

        $customer = Customer::create($validated);

        $subscribe->assignSubscription($customer , $validated['subscribe_uct']);

        return $this->redirectToIndex('مشتری با موفقیت ایجاد شد');
    }

    public function edit(Customer $customer): View
    {
        $subscribes = Subscribe::all();

        $crawlers = Crawler::all()->pluck('title', 'id')->toArray();

        return view('customer.edit', compact('customer', 'subscribes', 'crawlers'));
    }

    public function update(CustomerFormRequest $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validated();

        $customer->update($validated);

        return $this->redirectToIndex('مشتری با موفقیت بروزرسانی شد');
    }

    public function delete(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return $this->redirectToIndex('مشتری با موفقیت حذف شد');
    }

    public function resubscribe(ResubscribeRequest $request,SubscribeManagerInterface $subscribe,Customer $customer): RedirectResponse
    {
        $validated = $request->validated();

        $subscribe->assignSubscription($customer , $validated['subscribe_uct']);

        return $this->redirectToIndex('تمدید اشتراک با موفقیت انجام شد');
    }

    private function redirectToIndex(string $message): RedirectResponse
    {
        return redirect()->route('customer.index')->with('status', $message);
    }
}
