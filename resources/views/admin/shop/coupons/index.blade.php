@extends('layouts.admin')
@section('title', get_phrase('Coupons'))
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-18px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-ticket me-2"></i>
                {{ get_phrase('Coupons & Discounts') }}
            </h4>
            <button class="btn ol-btn-primary" data-bs-toggle="modal" data-bs-target="#addCouponModal">
                <i class="fi-rr-plus me-2"></i>{{ get_phrase('Add Coupon') }}
            </button>
        </div>
    </div>
</div>

<div class="ol-card mt-3">
    <div class="ol-card-body p-3">
        @if(count($coupons))
        <table id="datatable" class="table nowrap w-100">
            <thead>
                <tr>
                    <th>{{ get_phrase('ID') }}</th>
                    <th>{{ get_phrase('Code') }}</th>
                    <th>{{ get_phrase('Type') }}</th>
                    <th>{{ get_phrase('Value') }}</th>
                    <th>{{ get_phrase('Min Order') }}</th>
                    <th>{{ get_phrase('Usage') }}</th>
                    <th>{{ get_phrase('Expires') }}</th>
                    <th>{{ get_phrase('Status') }}</th>
                    <th>{{ get_phrase('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $num = 1 @endphp
                @foreach($coupons as $coupon)
                <tr>
                    <td>{{ $num++ }}</td>
                    <td>
                        <code class="bg-light px-2 py-1 rounded">{{ $coupon->code }}</code>
                    </td>
                    <td>
                        @if($coupon->type == 'percentage')
                            <span class="badge bg-info">{{ get_phrase('Percentage') }}</span>
                        @else
                            <span class="badge bg-primary">{{ get_phrase('Fixed') }}</span>
                        @endif
                    </td>
                    <td>
                        <strong>
                            @if($coupon->type == 'percentage')
                                {{ $coupon->value }}%
                            @else
                                {{ currency($coupon->value) }}
                            @endif
                        </strong>
                        @if($coupon->max_discount)
                            <br><small class="text-muted">Max: {{ currency($coupon->max_discount) }}</small>
                        @endif
                    </td>
                    <td>{{ $coupon->min_order_amount > 0 ? currency($coupon->min_order_amount) : '-' }}</td>
                    <td>
                        {{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}
                    </td>
                    <td>
                        @if($coupon->expires_at)
                            {{ date_formatter($coupon->expires_at) }}
                        @else
                            {{ get_phrase('Never') }}
                        @endif
                    </td>
                    <td>
                        @if($coupon->isValid())
                            <span class="badge bg-success">{{ get_phrase('Active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ get_phrase('Inactive') }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="dropdown ol-icon-dropdown">
                            <button class="px-2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="fi-rr-menu-dots-vertical"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item fs-14px" href="#" data-bs-toggle="modal" data-bs-target="#editCouponModal{{ $coupon->id }}"><i class="fi-rr-edit me-2"></i>{{ get_phrase('Edit') }}</a></li>
                                <li><a class="dropdown-item fs-14px" onclick="delete_modal('{{ route('admin.shop.coupon.delete', $coupon->id) }}')"><i class="fi-rr-trash me-2"></i>{{ get_phrase('Delete') }}</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>

                <div class="modal fade" id="editCouponModal{{ $coupon->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.shop.coupon.update', $coupon->id) }}" method="post">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ get_phrase('Edit Coupon') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">{{ get_phrase('Coupon Code') }} *</label>
                                        <input type="text" name="code" class="form-control" value="{{ $coupon->code }}" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ get_phrase('Type') }}</label>
                                                <select name="type" class="form-control">
                                                    <option value="percentage" {{ $coupon->type == 'percentage' ? 'selected' : '' }}>{{ get_phrase('Percentage') }}</option>
                                                    <option value="fixed" {{ $coupon->type == 'fixed' ? 'selected' : '' }}>{{ get_phrase('Fixed Amount') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ get_phrase('Value') }} *</label>
                                                <input type="number" name="value" class="form-control" value="{{ $coupon->value }}" step="0.01" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ get_phrase('Min Order Amount') }}</label>
                                                <input type="number" name="min_order_amount" class="form-control" value="{{ $coupon->min_order_amount }}" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ get_phrase('Max Discount') }}</label>
                                                <input type="number" name="max_discount" class="form-control" value="{{ $coupon->max_discount }}" step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ get_phrase('Usage Limit') }}</label>
                                                <input type="number" name="usage_limit" class="form-control" value="{{ $coupon->usage_limit }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ get_phrase('Status') }}</label>
                                                <select name="status" class="form-control">
                                                    <option value="1" {{ $coupon->status ? 'selected' : '' }}>{{ get_phrase('Active') }}</option>
                                                    <option value="0" {{ !$coupon->status ? 'selected' : '' }}>{{ get_phrase('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ get_phrase('Starts At') }}</label>
                                                <input type="date" name="starts_at" class="form-control" value="{{ $coupon->starts_at }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ get_phrase('Expires At') }}</label>
                                                <input type="date" name="expires_at" class="form-control" value="{{ $coupon->expires_at }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ get_phrase('Close') }}</button>
                                    <button type="submit" class="btn ol-btn-primary">{{ get_phrase('Update') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
        @else
            @include('layouts.no_data_found')
        @endif
    </div>
</div>

<div class="modal fade" id="addCouponModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.shop.coupon.store') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ get_phrase('Add New Coupon') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Coupon Code') }} *</label>
                        <div class="input-group">
                            <input type="text" name="code" id="coupon_code" class="form-control" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="generateCode()">{{ get_phrase('Generate') }}</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Type') }}</label>
                                <select name="type" class="form-control">
                                    <option value="percentage">{{ get_phrase('Percentage') }}</option>
                                    <option value="fixed">{{ get_phrase('Fixed Amount') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Value') }} *</label>
                                <input type="number" name="value" class="form-control" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Min Order Amount') }}</label>
                                <input type="number" name="min_order_amount" class="form-control" step="0.01" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Max Discount') }}</label>
                                <input type="number" name="max_discount" class="form-control" step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Usage Limit') }}</label>
                                <input type="number" name="usage_limit" class="form-control" placeholder="{{ get_phrase('Unlimited') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Status') }}</label>
                                <select name="status" class="form-control">
                                    <option value="1">{{ get_phrase('Active') }}</option>
                                    <option value="0">{{ get_phrase('Inactive') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Starts At') }}</label>
                                <input type="date" name="starts_at" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Expires At') }}</label>
                                <input type="date" name="expires_at" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ get_phrase('Close') }}</button>
                    <button type="submit" class="btn ol-btn-primary">{{ get_phrase('Create Coupon') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    function generateCode() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        for (let i = 0; i < 8; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('coupon_code').value = code;
    }
</script>
@endpush
