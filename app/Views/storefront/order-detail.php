<?= $this->extend('storefront/layout') ?>
<?= $this->section('page') ?>
  <sc-if value="{{ isOrderDetail }}" hint-placeholder-val="{{ false }}">
    <section style="max-width:1000px; margin:0 auto; padding:clamp(16px,3vw,36px) 16px clamp(36px,5vw,72px); display:flex; flex-direction:column; gap:18px">
      <button onClick="{{ selectOrdersTab }}" style="align-self:flex-start; background:none; border:0; padding:0; cursor:pointer; font-size:13px; color:#75666B" style-hover="color:#561530">← Back to orders</button>
      <div style="display:flex; flex-wrap:wrap; gap:14px; justify-content:space-between; align-items:flex-end">
        <div style="display:flex; flex-direction:column; gap:6px">
          <h1 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(28px,5vw,42px); margin:0; color:#561530">Order {{ od.no }}</h1>
          <span style="font-size:14px; color:#75666B">Placed {{ od.date }} · {{ od.payment }}</span>
        </div>
        <span style="border-radius:999px; padding:9px 16px; font-size:12px; font-weight:700; background:{{ od.statusBg }}; color:{{ od.statusFg }}">{{ od.status }}</span>
      </div>

      <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:22px; display:flex; flex-direction:column; gap:16px">
        <span style="font-size:11px; font-weight:700; letter-spacing:0.2em; color:#9E1C60">ORDER PROGRESS</span>
        <div style="display:flex; flex-wrap:wrap; gap:10px">
          <sc-for list="{{ od.timeline }}" as="t" hint-placeholder-count="5">
            <div style="flex:1 1 120px; display:flex; flex-direction:column; gap:8px">
              <div style="height:5px; border-radius:999px; background:{{ t.bar }}"></div>
              <span style="font-size:12px; font-weight:{{ t.weight }}; color:{{ t.color }}">{{ t.name }}</span>
            </div>
          </sc-for>
        </div>
      </div>

      <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:22px; display:flex; flex-direction:column; gap:14px">
        <span style="font-size:11px; font-weight:700; letter-spacing:0.2em; color:#9E1C60">ITEMS</span>
        <sc-for list="{{ od.items }}" as="it" hint-placeholder-count="3">
          <div style="display:flex; gap:14px; align-items:center; padding-bottom:12px; border-bottom:1px solid #EADFE2">
            <span style="position:relative; flex:none; display:block"><img src="{{ it.image }}" alt="{{ it.name }}" loading="lazy" style="width:60px; height:60px; border-radius:14px; object-fit:cover; flex:none"><sc-if value="{{ it.showOrigin }}" hint-placeholder-val="{{ false }}"><span title="{{ it.originTitle }}" style="position:absolute; right:-3px; bottom:-3px; width:16px; height:16px; border-radius:999px; border:2px solid #FFFFFF; background:{{ it.originBg }}; color:#FFFFFF; font-size:8.5px; font-weight:700; display:flex; align-items:center; justify-content:center; line-height:1">{{ it.originMark }}</span></sc-if></span>
            <div style="flex:1; min-width:0; display:flex; flex-direction:column; gap:3px">
              <span style="font-size:15px; font-weight:600; color:#2B171F">{{ it.name }}</span>
              <span style="font-size:12.5px; color:#75666B">{{ it.qty }} × {{ it.unit }}</span>
            </div>
            <span style="font-size:15px; font-weight:700; color:#561530; white-space:nowrap">{{ it.total }}</span>
          </div>
        </sc-for>
        <div style="display:flex; flex-direction:column; gap:8px; padding-top:4px">
          <div style="display:flex; justify-content:space-between; font-size:14px; color:#75666B"><span>Subtotal</span><span>{{ od.subtotal }}</span></div>
          <sc-if value="{{ od.hasDiscount }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; justify-content:space-between; font-size:14px; color:#811844"><span>Discount</span><span>−{{ od.discount }}</span></div>
          </sc-if>
          <div style="display:flex; justify-content:space-between; font-size:14px; color:#75666B"><span>Delivery fee</span><span>{{ od.delivery }}</span></div>
          <div style="display:flex; justify-content:space-between; align-items:baseline; padding-top:10px; border-top:1px solid #EADFE2"><span style="font-size:15px; font-weight:600">Total</span><span style="font-size:24px; font-weight:700; color:#561530">{{ od.total }}</span></div>
        </div>
      </div>

      <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:22px; display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,220px),1fr)); gap:16px">
        <div style="display:flex; flex-direction:column; gap:6px"><span style="font-size:11px; font-weight:700; letter-spacing:0.16em; color:#9E1C60">DELIVERING TO</span><span style="font-size:14px; line-height:1.65; color:#2B171F">{{ od.customer }}<br>{{ od.phone }}</span></div>
        <div style="display:flex; flex-direction:column; gap:6px"><span style="font-size:11px; font-weight:700; letter-spacing:0.16em; color:#9E1C60">ADDRESS</span><span style="font-size:14px; line-height:1.65; color:#2B171F">{{ od.address }}</span></div>
        <div style="display:flex; flex-direction:column; gap:6px"><span style="font-size:11px; font-weight:700; letter-spacing:0.16em; color:#9E1C60">NOTES</span><span style="font-size:14px; line-height:1.65; color:#75666B">{{ od.notes }}</span></div>
      </div>

      <div style="display:flex; flex-wrap:wrap; gap:10px">
        <button onClick="{{ reorder }}" style="background:#F5AD18; color:#561530; border:0; border-radius:16px; padding:15px 26px; font-size:13px; font-weight:700; cursor:pointer">Reorder</button>
        <button onClick="{{ downloadInvoice }}" style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:16px; padding:15px 24px; font-size:13px; font-weight:600; color:#561530; cursor:pointer">Download Invoice</button>
        <a href="https://wa.me/8801622823269" style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:16px; padding:15px 24px; font-size:13px; font-weight:600; color:#561530; text-decoration:none; display:inline-flex; align-items:center">Contact Us</a>
        <sc-if value="{{ od.cancellable }}" hint-placeholder-val="{{ false }}">
          <button onClick="{{ cancelOrder }}" style="background:none; border:1px solid #B3261E; border-radius:16px; padding:15px 24px; font-size:13px; font-weight:600; color:#B3261E; cursor:pointer" style-hover="background:#B3261E; color:#FFFFFF">Cancel Order</button>
        </sc-if>
      </div>
    </section>
  </sc-if>
<?= $this->endSection() ?>
