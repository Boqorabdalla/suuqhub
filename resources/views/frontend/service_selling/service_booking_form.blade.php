

@php 
    if($type == 'hotel'){
        $listing_creator_id = App\Models\HotelListing::where('id', $listing_id)->first();
    }elseif($type == 'car'){
        $listing_creator_id = App\Models\CarListing::where('id', $listing_id)->first();
    }elseif($type == 'beauty'){
        $listing_creator_id = App\Models\BeautyListing::where('id', $listing_id)->first();
    }elseif($type == 'real-estate'){
        $listing_creator_id = App\Models\RealEstateListing::where('id', $listing_id)->first();
    } elseif($type == 'restaurant'){
        $listing_creator_id = App\Models\RestaurantListing::where('id', $listing_id)->first();
    } else{
        $listing_creator_id = App\Models\CustomListings::where('id', $listing_id)->first();
    }
    
@endphp
<form  action="{{ route('service.booking.store') }}" method="post" enctype="multipart/form-data" id="form-action">
    @csrf
    <div class="mb-2">
        <input type="text" class="form-control ua-form-control" id="name" name="name" placeholder="{{get_phrase('Full name')}}">
    </div>
    <div class="mb-2">
        <input type="email" class="form-control ua-form-control" id="email" name="email" placeholder="{{get_phrase('Email')}}" >
    </div>
    <div class="mb-2">
        <input type="number" class="form-control ua-form-control" id="phone" name="phone" placeholder="{{get_phrase('Phone Number')}}" required="">
    </div>
    <div class="mb-2">
        <textarea class="form-control mform-control review-textarea" id="note" name="notes" placeholder="{{get_phrase('Any Note')}}" ></textarea>
    </div>
    <input type="hidden" name="type" value="{{$type}}">
    <input type="hidden" name="employeeid" value="{{$employeeid}}">
    <input type="hidden" name="listing_creator_id" value="{{$listing_creator_id->user_id}}">
    <input type="hidden" name="service_selling_id" value="{{$id}}">
    <input type="hidden" name="service_day" value="{{$day}}">
    <input type="hidden" name="service_time" value="{{$slot_time}}">
    <input type="hidden" name="service_date" value="{{$slot_date}}">
    <input type="hidden" name="listing_id" value="{{$listing_id}}">
    <div>
        <div class="d-flex gap-2" style="width: 382px; margin: auto;">
             <button type="submit" id="form-action-btn" class="submit-fluid-btn2" style="font-size: 14px; padding: 9px 23px;">{{get_phrase('Book This Service')}}</button>
             <a href="javascript:void(0);" class="submit-fluid-btn2 capitalize" style="font-size: 14px; padding: 9px 23px;" id="backBtn">{{get_phrase('Back')}}</a>
        </div>
        <span class="title mt-3 w-100 d-block text-center" style=" font-size: 16px; font-weight: 500; color: var(--themeColor); ">{{get_phrase('Payment Method : Pay after Service')}}</span>
    </div>
</form>




<script>

     $("#form-action-btn").on('click', function() {
            event.preventDefault();
            var name = $("#name").val();
            if (!name) {
                warning('Name is required');
            }
            var email = $("#email").val();
            if (!email) {
                warning('Email is required');
            }
            var phone = $("#phone").val();
            if (!phone) {
                warning('Phone Number is required');
            }
            var note = $("#note").val();
            if (!note) {
                warning('Note is required');
            }
            if (name && email && phone && note) {
                $("#form-action").trigger('submit');
            }
        });



document.getElementById('backBtn').addEventListener('click', function() {
    let type = document.querySelector('input[name="type"]').value;
    let listing_id = document.querySelector('input[name="listing_id"]').value;
    let id = document.querySelector('input[name="service_selling_id"]').value;

    serviceModal(
        'modal-xl',
        `{{ route('service.slot', ['type' => '__TYPE__', 'listing_id' => '__LISTINGID__', 'id' => '__ID__']) }}`
            .replace('__TYPE__', type)
            .replace('__LISTINGID__', listing_id)
            .replace('__ID__', id),
        '{{ get_phrase('Service Slot') }}'
    );
});
</script>
