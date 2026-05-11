<section class="recently-viewed-section">



    <div class="product-grid">

        @foreach($products as $product)

            <div class="product-card">

                <!-- CATEGORY BADGE -->
                <div class="product-badge">
                    {{ $product->category->name ?? 'Category' }}
                </div>

                <!-- PRODUCT IMAGE -->
                <div class="product-image-wrapper">

                    <img 
                        src="{{ asset($product->image ?? 'images/no-image.png') }}"
                        alt="{{ $product->name }}"
                        class="product-image"
                    >

                </div>

                <!-- CONTENT -->
                <div class="product-content">

                    <!-- TITLE -->
                    <h3 class="product-title">
                        {{ $product->name }}
                    </h3>

                    <!-- SHORT DESCRIPTION -->
                    <p class="product-description">
                        {{ Str::limit($product->short_description ?? 'Premium quality product with modern design and excellent performance.', 80) }}
                    </p>

                    <!-- ATTRIBUTES -->
                    <div class="attributes">

                
                        <div class="attribute-item">
                            <span class="label">Color</span>
                            <span class="value">
                                {{ $product->attribute->attribute_value_id ?? '-' }}
                            </span>
                        </div>

                        <div class="attribute-item">
                            <span class="label">Stock</span>
                            <span class="value stock">
                                {{ $product->stock ?? 0 }} Left
                            </span>
                        </div>

                    </div>

                    <!-- PRICE AREA -->
                    <div class="price-area">

                        <div>
                            <span class="old-price">
                                ₹{{ number_format($product->price + 500, 2) }}
                            </span>

                            <div class="product-price">
                                ₹{{ number_format($product->price, 2) }}
                            </div>
                        </div>

                        <div class="discount-badge">
                            20% OFF
                        </div>

                    </div>

                    <!-- BUTTONS -->
                    <div class="button-group">

                        <button class="cart-btn">
                            Add to Cart
                        </button>

                        <button class="wishlist-btn">
                            ♥
                        </button>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

</section>

<style>

*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.recently-viewed-section{
    width: 92%;
    margin: 60px auto;
}

/* HEADER */

.section-header{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
    flex-wrap: wrap;
    gap: 15px;
}

.section-header h2{
    font-size: 34px;
    font-weight: 700;
    color: #111;
    margin-bottom: 8px;
}

.section-header p{
    color: #777;
    font-size: 15px;
}

.view-all-btn{
    padding: 12px 22px;
    background: #111;
    color: #fff;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}

.view-all-btn:hover{
    background: #e63946;
}

/* GRID */

.product-grid{
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 28px;
}

/* CARD */

.product-card{
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    position: relative;
    border: 1px solid #eee;
    box-shadow: 0 5px 25px rgba(0,0,0,0.06);
    transition: 0.35s ease;
}

.product-card:hover{
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.12);
}

/* BADGE */

.product-badge{
    position: absolute;
    top: 15px;
    left: 15px;
    background: #e63946;
    color: #fff;
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 600;
    z-index: 2;
}

/* IMAGE */

.product-image-wrapper{
    width: 100%;
    height: 260px;
    background: #f8f8f8;
    overflow: hidden;
}

.product-image{
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: 0.5s ease;
}

.product-card:hover .product-image{
    transform: scale(1.08);
}

/* CONTENT */

.product-content{
    padding: 22px;
}

.product-title{
    font-size: 20px;
    font-weight: 700;
    color: #111;
    margin-bottom: 10px;
    line-height: 1.4;
}

.product-description{
    font-size: 14px;
    color: #666;
    line-height: 1.7;
    margin-bottom: 18px;
}

/* ATTRIBUTES */

.attributes{
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
}

.attribute-item{
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f7f7f7;
    padding: 10px 14px;
    border-radius: 10px;
}

.label{
    color: #666;
    font-size: 14px;
}

.value{
    font-weight: 600;
    color: #111;
}

.stock{
    color: green;
}

/* PRICE */

.price-area{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 22px;
}

.old-price{
    color: #999;
    text-decoration: line-through;
    font-size: 14px;
}

.product-price{
    font-size: 26px;
    font-weight: 700;
    color: #e63946;
    margin-top: 3px;
}

.discount-badge{
    background: #111;
    color: #fff;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
}

/* BUTTONS */

.button-group{
    display: flex;
    gap: 12px;
}

.cart-btn{
    flex: 1;
    padding: 13px;
    border: none;
    border-radius: 12px;
    background: #111;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

.cart-btn:hover{
    background: #e63946;
}

.wishlist-btn{
    width: 55px;
    border: none;
    border-radius: 12px;
    background: #f3f3f3;
    font-size: 18px;
    cursor: pointer;
    transition: 0.3s;
}

.wishlist-btn:hover{
    background: #e63946;
    color: #fff;
}

/* MOBILE */

@media(max-width:768px){

    .section-header{
        flex-direction: column;
        align-items: flex-start;
    }

    .section-header h2{
        font-size: 28px;
    }

    .product-image-wrapper{
        height: 220px;
    }

}

</style>