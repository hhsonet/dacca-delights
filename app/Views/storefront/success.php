<?= $this->extend('storefront/layout') ?>
<?= $this->section('page') ?>
  <sc-if value="{{ isSuccess }}" hint-placeholder-val="{{ false }}">
    <section class="invsection" style="max-width:560px; margin:0 auto; padding:clamp(36px,8vw,96px) 16px; display:flex; flex-direction:column; gap:18px; align-items:center; text-align:center">
      <span class="invhide" style="width:76px; height:76px; border-radius:999px; background:#F5AD18; display:flex; align-items:center; justify-content:center; font-size:32px; color:#561530">✓</span>
      <h1 class="invhide" style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(32px,6vw,52px); margin:0; color:#561530; line-height:1.05">Order Confirmed</h1>
      <p class="invhide" style="margin:0; font-size:15px; line-height:1.7; color:#75666B; max-width:44ch">Order {{ orderNo }} is in the bake queue for {{ paidDate }}. We'll message you when the rider leaves the kitchen, usually between 7 and 10am.</p>
      <div class="noprint invhide" style="width:100%; display:flex; flex-wrap:wrap; gap:9px; justify-content:center">
        <button onClick="{{ savePng }}" disabled="{{ exportBusy }}" style="background:#561530; color:#FFF9F1; border:0; border-radius:14px; padding:13px 22px; font-size:13px; font-weight:600; cursor:pointer">Save as Image</button>
        <button onClick="{{ savePdf }}" style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:14px; padding:13px 22px; font-size:13px; font-weight:600; color:#561530; cursor:pointer" style-hover="border-color:#561530">Save as PDF</button>
        <button onClick="{{ shareInvoice }}" style="background:#F5AD18; color:#561530; border:0; border-radius:14px; padding:13px 22px; font-size:13px; font-weight:700; cursor:pointer">Share</button>
      </div>
      <sc-if value="{{ exportNote }}" hint-placeholder-val="{{ false }}">
        <span class="noprint invhide" style="font-size:12.5px; font-weight:600; color:#811844">{{ exportNote }}</span>
      </sc-if>

      <div ref="{{ invoiceRef }}" class="invoice" style="width:100%; max-width:384px; margin:0 auto; background:#FFFFFF; border:1px solid #EADFE2; border-radius:14px; padding:20px; display:flex; flex-direction:column; gap:16px; text-align:left">

        <div style="display:flex; flex-direction:column; gap:12px; border-bottom:1px solid #EADFE2; padding-bottom:14px">
          <div style="display:flex; flex-direction:column; gap:2px">
            <img src="<?= base_url("assets/storefront/logo.svg") ?>" alt="Dacca Delights" style="height: 101px; width: 92px; object-fit: cover; align-self: flex-start; display: block; margin-bottom: 4px">
            <span style="font-size:11.5px; color:#75666B; padding-top:6px">Kafrul, Dhaka Cantonment, Dhaka 1206</span>
            <span style="font-size:11.5px; color:#75666B">+880 1622 823269 · info@daccadelights.com</span>
          </div>
          <div style="display:flex; flex-direction:column; gap:3px; align-items:flex-start">
            <span style="background:#FDF0D3; color:#561530; border-radius:999px; padding:5px 12px; font-size:10.5px; font-weight:700; letter-spacing:0.1em">CONFIRMED</span>
            <span style="font-size:13px; font-weight:700; color:#561530; padding-top:4px">INVOICE {{ orderNo }}</span>
            <span style="font-size:11.5px; color:#75666B">Issued on: {{ invoice.issued }}</span>
            <span style="font-size:11.5px; color:#75666B">{{ invoice.issuedTime }}</span>
          </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:14px; background:#FFF9F1; border-radius:12px; padding:14px">
          <div style="display:flex; flex-direction:column; gap:3px">
            <span style="font-size:10.5px; font-weight:700; letter-spacing:0.16em; text-transform:uppercase; color:#9E1C60; margin-bottom:3px">Customer details</span>
            <span style="font-size:13.5px; font-weight:700; color:#2B171F">{{ invoice.customer }}</span>
            <span style="font-size:12px; line-height:1.7; color:#4B3B40"><span style="font-weight:600">Contact number:</span> {{ invoice.phone }}</span>
            <span style="font-size:12px; line-height:1.7; color:#4B3B40"><span style="font-weight:600">WhatsApp Number:</span> {{ invoice.whatsapp }}</span>
            <sc-if value="{{ invoice.hasEmail }}" hint-placeholder-val="{{ false }}">
              <span style="font-size:12px; line-height:1.7; color:#4B3B40"><span style="font-weight:600">Email:</span> {{ invoice.email }}</span>
            </sc-if>
            <span style="font-size:12px; line-height:1.7; color:#4B3B40"><span style="font-weight:600">Address:</span> {{ invoice.address }}</span>
            <sc-if value="{{ invoice.hasMap }}" hint-placeholder-val="{{ false }}">
              <a href="{{ invoice.map }}" target="_blank" rel="noreferrer" style="font-size:12px; font-weight:600; color:#811844; text-decoration:underline">View map location</a>
            </sc-if>
          </div>
          <div style="display:flex; flex-direction:column; gap:3px">
            <span style="font-size:10.5px; font-weight:700; letter-spacing:0.16em; text-transform:uppercase; color:#9E1C60; margin-bottom:3px">Fulfilment &amp; payment</span>
            <span style="font-size:12px; line-height:1.7; color:#4B3B40"><span style="font-weight:600">Method:</span> {{ invoice.method }}</span>
            <span style="font-size:12px; line-height:1.7; color:#4B3B40"><span style="font-weight:600">Delivery Location:</span> {{ invoice.zoneLine }}</span>
            <span style="font-size:12px; line-height:1.7; color:#4B3B40"><span style="font-weight:600">Delivery Date:</span> {{ invoice.date }}</span>
            <span style="font-size:12px; line-height:1.7; color:#4B3B40"><span style="font-weight:600">Payment Method:</span> {{ invoice.payment }}</span>
            <span style="font-size:12px; line-height:1.7; color:#4B3B40"><span style="font-weight:600">Payment Status:</span> <span style="font-weight:700; color:{{ invoice.statusColor }}">{{ invoice.status }}</span></span>
          </div>
        </div>

        <div style="display:flex; flex-direction:column">
          <div style="display:grid; grid-template-columns:1fr 44px 68px; gap:8px; border-bottom:1px solid #EADFE2; padding-bottom:8px">
            <span style="font-size:10px; font-weight:700; letter-spacing:0.14em; text-transform:uppercase; color:#9E1C60">Item/s</span>
            <span style="font-size:10px; font-weight:700; letter-spacing:0.14em; text-transform:uppercase; color:#9E1C60; text-align:center">Qty</span>
            <span style="font-size:10px; font-weight:700; letter-spacing:0.14em; text-transform:uppercase; color:#9E1C60; text-align:right">Total</span>
          </div>
          <sc-for list="{{ invoice.items }}" as="it" hint-placeholder-count="3">
            <div style="display:grid; grid-template-columns:1fr 44px 68px; gap:8px; border-bottom:1px solid #F5EDEF; padding:10px 0; align-items:start">
              <span style="display:flex; flex-direction:column; gap:4px">
                <span style="font-size:12.5px; font-weight:600; color:#2B171F; line-height:1.4">{{ it.name }}</span>
                <span style="display:flex; flex-wrap:wrap; gap:4px">
                  <sc-for list="{{ it.options }}" as="o" hint-placeholder-count="1">
                    <span style="background:#F7EEF1; color:#561530; border-radius:5px; padding:2px 6px; font-size:10px; font-weight:600">{{ o }}</span>
                  </sc-for>
                </span>
              </span>
              <span style="font-size:12px; color:#4B3B40; text-align:center">{{ it.qty }}</span>
              <span style="font-size:12.5px; font-weight:700; color:#2B171F; text-align:right; white-space:nowrap">{{ it.total }}</span>
            </div>
          </sc-for>
        </div>

        <sc-if value="{{ invoice.hasNote }}" hint-placeholder-val="{{ false }}">
          <div style="background:#FFF9F1; border-left:3px solid #F5AD18; border-radius:0 10px 10px 0; padding:11px 14px; display:flex; flex-direction:column; gap:2px">
            <span style="font-size:11.5px; font-weight:700; color:#561530">Customer note</span>
            <span style="font-size:12.5px; font-style:italic; line-height:1.6; color:#4B3B40">“{{ invoice.note }}”</span>
          </div>
        </sc-if>

        <div style="display:flex; justify-content:flex-end; border-top:1px solid #EADFE2; padding-top:16px">
          <div style="width:100%; display:flex; flex-direction:column; gap:6px">
            <div style="display:flex; justify-content:space-between; font-size:12.5px; color:#75666B"><span>Subtotal</span><span>{{ invoice.subtotal }}</span></div>
            <sc-if value="{{ invoice.hasDiscount }}" hint-placeholder-val="{{ false }}">
              <div style="display:flex; justify-content:space-between; font-size:12.5px; color:#75666B"><span>Discount</span><span>− {{ invoice.discount }}</span></div>
            </sc-if>
            <div style="display:flex; justify-content:space-between; font-size:12.5px; color:#75666B"><span>Delivery fee</span><span>{{ invoice.delivery }}</span></div>
            <div style="display:flex; justify-content:space-between; border-top:1px solid #EADFE2; padding-top:8px; font-size:14.5px; font-weight:700; color:#561530"><span>Grand total</span><span>{{ paidTotal }}</span></div>
          </div>
        </div>

        <div style="border-top:1px solid #EADFE2; padding-top:16px; display:flex; flex-direction:column; gap:4px; align-items:center; text-align:center">
          <span style="font-family:'Fraunces',serif; font-weight:600; font-size:14.5px; color:#561530">Thank you for your order! :D</span>
          <span style="font-size: 10.5px; color: #75666B">Your fresh batch will be ready soon!</span>
          <span style="font-size:11px; color:#9C8D92; max-width:52ch; line-height:1.6">Questions? WhatsApp us at +880 1622 823269</span>
        </div>
      </div>

      <div class="noprint invhide" style="display:flex; gap:12px; flex-wrap:wrap; justify-content:center">
        <button onClick="{{ goOrders }}" style="background:#561530; color:#FFF9F1; border:0; border-radius:16px; padding:15px 26px; font-size:14px; font-weight:600; cursor:pointer">Track Order</button>
        <button onClick="{{ goMenu }}" style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:16px; padding:15px 26px; font-size:14px; font-weight:600; color:#561530; cursor:pointer">Keep Shopping</button>
      </div>
    </section>
  </sc-if>
<?= $this->endSection() ?>
