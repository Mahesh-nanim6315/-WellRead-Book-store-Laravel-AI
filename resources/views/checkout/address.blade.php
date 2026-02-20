@include('common.header')

<div class="checkout-container">
    <h2>Checkout</h2>
    
    <div class="checkout-grid">
        <!-- LEFT COLUMN: ADDRESS OR RENTAL MESSAGE -->
        <div>
            @if($needsAddress)
                <!-- ADDRESS FORM - Show when cart has paperback -->
                <div class="checkout-card">
                    <h3>📦 Delivery Address Required</h3>
                    <div class="cart-info">
                        <p>Your cart contains <strong>paperback</strong> items that require shipping.</p>
                        <p>Please provide your delivery address below.</p>
                    </div>
                    
                    <form method="POST" action="{{ route('checkout.process') }}">
                        @csrf
                        <input type="hidden" name="requires_shipping" value="1">
                        
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="full_name" value="{{ old('full_name', auth()->user()->name) }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Phone Number *</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Full Address *</label>
                            <textarea name="address_line" placeholder="House/Flat no, Building, Street, Area..." required>{{ old('address_line') }}</textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>City *</label>
                                <input type="text" name="city" value="{{ old('city') }}" required>
                            </div>
                            <div class="form-group">
                                <label>State *</label>
                                <input type="text" name="state" value="{{ old('state') }}" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Pincode *</label>
                            <input type="text" name="pincode" value="{{ old('pincode') }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Country *</label>
                            <input type="text" name="country" value="{{ old('country', 'India') }}">
                        </div>
                        
                        <button type="submit" class="btn-primary">
                            Continue to Payment
                        </button>
                    </form>
                </div>
            @else
                <!-- RENTAL ITEMS ONLY - No address needed -->
                <div class="checkout-card">
                    <h3>🎧 Digital Purchase</h3>
                    <div class="rental-notice">
                        <div class="rental-icon">
                            <span>📖</span>
                            <span>🎧</span>
                        </div>
                        <p>Your cart contains only <strong>eBook and/or Audiobook</strong> digital items.</p>
                        <p>No delivery address is required.</p>
                        <div class="rental-details">
                            <p><strong>Access Details:</strong></p>
                            <ul>
                                <li>eBooks: Access via your digital library</li>
                                <li>Audiobooks: Stream or download from your account</li>
                                <li>Available immediately after payment</li>
                            </ul>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('checkout.process') }}">
                        @csrf
                        <input type="hidden" name="rental_only" value="1">
                        <button type="submit" class="btn-primary">
                            Continue to Payment
                        </button>
                    </form>
                </div>
            @endif
        </div>
        
        <!-- RIGHT COLUMN: ORDER SUMMARY -->
        <div>
            <div class="checkout-card">
                <h3>Order Summary</h3>
                
                <!-- Cart Items -->
                @foreach($cart->items as $item)
                    <div class="summary-item">
                        <div class="item-info">
                            <strong>{{ $item->book->name }}</strong>
                            <div class="item-details">
                                <span class="format-badge {{ strtolower($item->format) }}">
                                    @if(strtolower($item->format) === 'paperback')
                                        <span class="format-icon">📦</span>
                                    @elseif(strtolower($item->format) === 'ebook')
                                        <span class="format-icon">📖</span>
                                    @else
                                        <span class="format-icon">🎧</span>
                                    @endif
                                    {{ ucfirst($item->format) }}
                                </span>
                                <span class="quantity">Qty: {{ $item->quantity }}</span>
                                
                                @if(in_array(strtolower($item->format), ['ebook', 'audiobook']))
                                    <span class="rental-badge">Digital</span>
                                @else
                                    <span class="shipping-badge">Shipping</span>
                                @endif
                            </div>
                        </div>
                        <div class="item-price">
                            ₹{{ number_format($item->price * $item->quantity, 2) }}
                        </div>
                    </div>
                @endforeach
                
                <hr>
                
                <!-- Price Breakdown -->
                <div class="order-breakdown">
                    <div class="breakdown-item">
                        <span>Subtotal:</span>
                        <span>₹{{ number_format($subtotal, 2) }}</span>
                    </div>
                    
                    <div class="breakdown-item">
                        <span>Tax (5%):</span>
                        <span>₹{{ number_format($tax, 2) }}</span>
                    </div>
                    
                    @if(isset($discount) && $discount > 0)
                        <div class="breakdown-item discount">
                            <span>Discount:</span>
                            <span>-₹{{ number_format($discount, 2) }}</span>
                        </div>
                        
                        @if(isset($couponCode) && $couponCode)
                            <div class="coupon-applied">
                                Coupon Applied: <strong>{{ $couponCode }}</strong>
                            </div>
                        @endif
                    @endif
                    
                    <div class="breakdown-item total">
                        <span>Total Amount:</span>
                        <span>₹{{ number_format($total, 2) }}</span>
                    </div>
                </div>
                
                <!-- Item Type Summary -->
                <div class="type-summary">
                    @php
                        $paperbackCount = $cart->items->filter(function($item) {
                            return strtolower($item->format) === 'paperback';
                        })->count();
                        
                        $ebookCount = $cart->items->filter(function($item) {
                            return strtolower($item->format) === 'ebook';
                        })->count();
                        
                        $audiobookCount = $cart->items->filter(function($item) {
                            return strtolower($item->format) === 'audiobook';
                        })->count();
                    @endphp
                    
                    @if($paperbackCount > 0)
                        <div class="type-item">
                            <span>📦 Paperback (Physical):</span>
                            <span>{{ $paperbackCount }} item(s)</span>
                        </div>
                    @endif
                    
                    @if($ebookCount > 0)
                        <div class="type-item">
                            <span>📖 eBook (Digital):</span>
                            <span>{{ $ebookCount }} item(s)</span>
                        </div>
                    @endif
                    
                    @if($audiobookCount > 0)
                        <div class="type-item">
                            <span>🎧 Audiobook (Digital):</span>
                            <span>{{ $audiobookCount }} item(s)</span>
                        </div>
                    @endif
                </div>
                
                <!-- Delivery/Rental Note -->
                <div class="delivery-note {{ $needsAddress ? 'physical-note' : 'rental-note' }}">
                    @if($needsAddress)
                        <p>📦 <strong>Shipping:</strong> Paperback items will be shipped to your address.</p>
                        @if($ebookCount > 0 || $audiobookCount > 0)
                            <p>⚡ <strong>Digital Access:</strong> eBook/Audiobook available immediately.</p>
                        @endif
                    @else
                        <p>⚡ <strong>Instant Access:</strong> All items are digital and available immediately.</p>
                        <p><small>No shipping charges or delivery time required.</small></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('common.footer')