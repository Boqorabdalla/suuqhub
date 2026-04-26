@extends('layouts.admin')
@push('title', get_phrase('Payout History'))
@section('content')
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{ get_phrase('Payout History') }}</h4>
                        <div class="page-title-right">
                            <a href="{{ route('agent.earnings') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>{{ get_phrase('Back to Earnings') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    @if($payouts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>{{ get_phrase('Date') }}</th>
                                        <th>{{ get_phrase('Amount') }}</th>
                                        <th>{{ get_phrase('Method') }}</th>
                                        <th>{{ get_phrase('Transaction ID') }}</th>
                                        <th>{{ get_phrase('Status') }}</th>
                                        <th>{{ get_phrase('Notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payouts as $payout)
                                    <tr>
                                        <td>{{ date_formatter($payout->created_at) }}</td>
                                        <td><strong>{{ currency($payout->amount) }}</strong></td>
                                        <td>{{ ucwords(str_replace('_', ' ', $payout->payment_method)) }}</td>
                                        <td>
                                            @if($payout->transaction_id)
                                                <code>{{ $payout->transaction_id }}</code>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($payout->status == 'pending')
                                                <span class="badge bg-warning text-dark">{{ get_phrase('Pending') }}</span>
                                            @elseif($payout->status == 'processing')
                                                <span class="badge bg-info">{{ get_phrase('Processing') }}</span>
                                            @elseif($payout->status == 'completed')
                                                <span class="badge bg-success">{{ get_phrase('Completed') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ get_phrase('Rejected') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($payout->notes)
                                                <small>{{ Str::limit($payout->notes, 50) }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $payouts->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-wallet2 text-muted" style="font-size: 64px;"></i>
                            <h5 class="mt-3 text-muted">{{ get_phrase('No payout history') }}</h5>
                            <p class="text-muted">{{ get_phrase('Your payout requests will appear here') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
