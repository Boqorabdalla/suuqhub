
<script src="{{ asset('assets/frontend/js/mixitup.min.js') }}"></script>
<div class="at-details-description shopdescription mb-50px mt-5">
    <h4 class="title mb-16">{{ get_phrase('Service Selling') }}</h4>

        <div class="row  g-2">
           @foreach($serviceSselling as $selling)
                <div class="col-lg-6 col-md-6 shopItemCard">
                    <div class="shopCard">
                        <figure>
                            <a href="{{get_all_image('service_selling/'. $selling->image)}}" class="veno-gallery-img w-100 d-block">
                                <img style="height: 145px;" src="{{get_all_image('service_selling/'. $selling->image)}}" alt="...">
                            </a>
                            
                        </figure>
                        <div class="figure-body">
                            <div class="fTitile d-flex justify-content-between">
                                <h4 style="margin-bottom : 3px;">{{$selling->name}}</h4>
                            </div>
                            <p class="mb-2 name">{{$selling->description}}</p>
                            @php
                                $durationInMinutes = $selling->duration;
                                $hours = floor($durationInMinutes / 60);
                                $minutes = $durationInMinutes % 60;
                            @endphp

                            <p class="mb-2 name">
                                {{ get_phrase('Duration') }} :
                                {{ $hours > 0 ? $hours . ' hour' . ($hours > 1 ? 's' : '') : '' }}
                                {{ $minutes > 0 ? $minutes . ' minute' . ($minutes > 1 ? 's' : '') : '' }}
                            </p>
                            <div class="product-container">
                                <span class="price">{{currency($selling->price)}}</span>
                                <div class="quantity-selector" style="border: none;">    
                                    <a 
                                        href="javascript:void(0);" 
                                        class="gray-btn1" 
                                        style="padding: 4px 13px; font-size: 13px;" 
                                        onclick="serviceModal('modal-xl','{{ route('service.slot', ['type' => $selling->type, 'listing_id' =>$selling->listing_id , 'id' => $selling->id]) }}','{{ get_phrase('Service Slot') }}')">
                                        {{ get_phrase('Book Now') }}
                                    </a>
                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

       </div>
</div>

