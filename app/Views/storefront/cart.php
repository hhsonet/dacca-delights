<?= $this->extend('storefront/layout') ?>
<?= $this->section('page') ?>
  <sc-if value="{{ isCart }}" hint-placeholder-val="{{ false }}">
    <section style="max-width:1200px; margin:0 auto; padding:clamp(20px,3vw,44px) 16px clamp(36px,5vw,72px)">
      <h1 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(32px,6vw,52px); margin:0 0 22px; color:#561530">Your Cart</h1>
      <div style="display:flex; flex-wrap:wrap; gap:24px; align-items:flex-start">
        <div style="flex:3 1 380px; min-width:0; display:flex; flex-direction:column; gap:12px">
          <sc-for list="{{ cartLines }}" as="l" hint-placeholder-count="2">
            <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:14px; display:flex; gap:14px; align-items:center">
              <img src="{{ l.image }}" alt="{{ l.name }}" loading="lazy" onError="{{ onImgError }}" style="width:84px; height:84px; border-radius:18px; object-fit:cover; flex:none">
              <div style="flex:1; min-width:0; display:flex; flex-direction:column; gap:8px">
                <div style="display:flex; justify-content:space-between; gap:10px; align-items:flex-start">
                  <div style="display:flex; flex-direction:column; gap:3px; min-width:0">
                    <span style="font-family:'Fraunces',serif; font-weight:600; font-size:19px; line-height:1.28; letter-spacing:0.5px; color:#2B171F">{{ l.name }}</span>
                    <sc-if value="{{ l.hasOptions }}" hint-placeholder-val="{{ false }}">
                      <span style="font-size:11.5px; font-weight:700; letter-spacing:0.04em; color:#9E1C60">{{ l.options }}</span>
                    </sc-if>
                    <sc-if value="{{ l.hasNote }}" hint-placeholder-val="{{ false }}">
                      <span style="font-size:11.5px; line-height:1.5; color:#75666B">Note: {{ l.note }}</span>
                    </sc-if>
                    <span style="font-size:12px; color:#75666B">{{ l.unitLine }}</span>
                  </div>
                  <span style="font-size:16px; font-weight:700; color:#561530; white-space:nowrap">{{ l.lineTotal }}</span>
                </div>
                <div style="display:flex; align-items:center; gap:12px">
                  <div style="display:flex; align-items:center; border:1px solid #EADFE2; border-radius:14px">
                    <button onClick="{{ l.dec }}" aria-label="Decrease" style="width:40px; height:40px; background:none; border:0; cursor:pointer; font-size:17px; color:#561530">−</button>
                    <span style="min-width:28px; text-align:center; font-size:14px; font-weight:600">{{ l.qty }}</span>
                    <button onClick="{{ l.inc }}" aria-label="Increase" style="width:40px; height:40px; background:none; border:0; cursor:pointer; font-size:17px; color:#561530">+</button>
                  </div>
                  <button onClick="{{ l.remove }}" style="background:none; border:0; cursor:pointer; font-size:13px; color:#75666B; padding:0" style-hover="color:#9E1C60">Remove</button>
                </div>
              </div>
            </div>
          </sc-for>
          <sc-if value="{{ cartEmpty }}" hint-placeholder-val="{{ true }}">
            <div style="background:#FFFFFF; border:1px dashed #EADFE2; border-radius:24px; padding:56px 24px; display:flex; flex-direction:column; gap:14px; align-items:center; text-align:center">
              <span style="font-family:'Fraunces',serif; font-size:26px; color:#561530">Your cart is empty</span>
              <span style="font-size:14px; color:#75666B">The oven is on. Go pick something.</span>
              <button onClick="{{ goMenu }}" style="background:#F5AD18; color:#561530; border:0; border-radius:16px; padding:15px 26px; font-size:14px; font-weight:700; cursor:pointer">Explore Menu</button>
            </div>
          </sc-if>
        </div>
        <aside style="flex:1 1 280px; max-width:380px; position:sticky; top:88px; background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:22px; display:flex; flex-direction:column; gap:14px">
          <span style="font-size:11px; font-weight:700; letter-spacing:0.2em; color:#75666B">ORDER SUMMARY</span>
          <div style="display:flex; gap:8px">
            <input value="{{ coupon }}" onChange="{{ onCoupon }}" placeholder="Coupon code" style="flex:1; min-width:0; border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:13px 14px; font-size:14px; color:#2B171F">
            <button onClick="{{ applyCoupon }}" style="border:0; border-radius:14px; background:#9E1C60; color:#FFFFFF; padding:13px 18px; font-size:13px; font-weight:600; cursor:pointer">Apply</button>
          </div>
          <sc-if value="{{ couponApplied }}" hint-placeholder-val="{{ false }}">
            <span style="font-size:12px; color:#811844; font-weight:600">{{ couponMessage }}</span>
          </sc-if>
          <div style="display:flex; justify-content:space-between; font-size:14px; color:#75666B"><span>Subtotal</span><span>{{ subtotal }}</span></div>
          <sc-if value="{{ couponApplied }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; justify-content:space-between; font-size:14px; color:#811844"><span>Discount</span><span>−{{ discount }}</span></div>
          </sc-if>
          <div style="display:flex; justify-content:space-between; font-size:14px; color:#75666B"><span>Delivery Fee</span><span>{{ deliveryLabel }}</span></div>
          <sc-if value="{{ moqWarn }}" hint-placeholder-val="{{ false }}">
            <div style="background:#FFF4DC; border-left:3px solid #F5AD18; border-radius:0 12px 12px 0; padding:13px 15px; display:flex; flex-direction:column; gap:7px; align-items:flex-start">
              <span style="font-size:13px; font-weight:700; color:#561530">{{ moqTitle }}</span>
              <span style="font-size:12.5px; line-height:1.6; color:#75666B">{{ moqBody }}</span>
              <button onClick="{{ moqFix }}" style="background:#561530; color:#FFF9F1; border:0; border-radius:10px; padding:9px 15px; font-size:12px; font-weight:600; cursor:pointer">{{ moqTitle }}</button>
            </div>
          </sc-if>
          <div style="display:flex; justify-content:space-between; align-items:baseline; padding-top:14px; border-top:1px solid #EADFE2"><span style="font-size:15px; font-weight:600">Total</span><span style="font-size:26px; font-weight:700; color:#561530">{{ total }}</span></div>
          <button onClick="{{ goCheckout }}" disabled="{{ moqWarn }}" style="background:{{ moqBtnBg }}; color:#561530; border:0; border-radius:16px; padding:17px; font-size:15px; font-weight:700; cursor:pointer; transition:background 160ms ease" style-hover="background:#561530; color:#FFF9F1">Proceed to Checkout</button>
          <a href="https://wa.me/8801622823269" style="text-align:center; font-size:13px">Custom or bulk order? Message us →</a>
        </aside>
      </div>
    </section>
  </sc-if>
<?= $this->endSection() ?>
