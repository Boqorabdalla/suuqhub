@extends('layouts.admin')
@section('title', get_phrase('Commission Settings'))

@section('admin_layout')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Commission Settings</h4>
        <a href="{{ route('admin.shop.subscriptions.plans') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Plans
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <div class="alert alert-info">
            <h5><i class="bi bi-info-circle"></i> How Commission Works</h5>
            <ul class="mb-0">
                <li><strong>Commission</strong> is taken from each sale the agent makes.</li>
                <li><strong>Plan Commission:</strong> Uses the commission rate from the agent's subscription plan.</li>
                <li><strong>Global Commission:</strong> Override all plans with a single rate (set below).</li>
                <li><strong>Currently set to 0%</strong> - No commission is taken. You can enable it later.</li>
            </ul>
        </div>

        <form action="{{ route('admin.shop.subscriptions.commission.update') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Global Commission Settings</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Default Commission Rate (%)</label>
                                <input type="number" name="commission_rate" class="form-control" 
                                    value="{{ $globalCommission ?? 0 }}" 
                                    min="0" max="100" step="0.01" required>
                                <small class="text-muted">Set to 0 for no commission. Set to 10 for 10% commission per sale.</small>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input type="checkbox" name="use_global" class="form-check-input" id="use_global" 
                                    {{ ($useGlobalCommission ?? 0) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="use_global">
                                    Use global commission rate for all agents (override plan rates)
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Settings
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Plan-Based Commission (Default)</h5>
                            <p class="text-muted">Each subscription plan has its own commission rate. Agents will pay commission based on their plan unless you enable global commission above.</p>
                            
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Plan</th>
                                        <th>Commission Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(\App\Models\SubscriptionPlan::where('status', 1)->get() as $plan)
                                    <tr>
                                        <td>{{ $plan->name }}</td>
                                        <td><span class="badge bg-secondary">{{ $plan->commission_rate }}%</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="mb-3">Commission Example</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <strong>Sale Amount:</strong> $100
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <strong>Commission Rate:</strong> {{ $globalCommission ?? 0 }}%
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <strong>Platform Earns:</strong> ${{ number_format(100 * (($globalCommission ?? 0) / 100), 2) }}
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="p-3 bg-success text-white rounded">
                            <strong>Agent Receives:</strong> ${{ number_format(100 * (1 - (($globalCommission ?? 0) / 100)), 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
