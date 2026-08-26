<?= $this->extend('storefront/layout') ?>
<?= $this->section('page') ?>
  <sc-if value="{{ isBulk }}" hint-placeholder-val="{{ false }}">
    <section style="max-width:900px; margin:0 auto; padding:clamp(20px,3vw,44px) 16px clamp(36px,5vw,72px)">
      <sc-if value="{{ bulkSent }}" hint-placeholder-val="{{ false }}">
        <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:28px; padding:clamp(30px,6vw,64px); display:flex; flex-direction:column; gap:16px; align-items:flex-start">
          <span style="width:64px; height:64px; border-radius:999px; background:#F5AD18; color:#561530; font-size:28px; display:flex; align-items:center; justify-content:center">✓</span>
          <h1 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(30px,5vw,44px); margin:0; color:#561530">Thank you!</h1>
          <p style="margin:0; font-size:15px; line-height:1.7; color:#75666B; max-width:46ch">Our team will contact you shortly to confirm quantities, pricing and delivery for your order.</p>
          <button onClick="{{ goHome }}" style="background:#561530; color:#FFF9F1; border:0; border-radius:16px; padding:15px 26px; font-size:14px; font-weight:600; cursor:pointer">Back to Home</button>
        </div>
      </sc-if>
      <sc-if value="{{ bulkForm }}" hint-placeholder-val="{{ true }}">
        <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:22px">
          <span style="font-size:11px; font-weight:700; letter-spacing:0.24em; color:#9E1C60">BULK &amp; WHOLESALE</span>
          <h1 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(32px,6vw,52px); margin:0; color:#561530">Bulk Order Inquiry</h1>
          <p style="margin:0; font-size:15px; line-height:1.7; color:#75666B; max-width:52ch">Tell us what you need and when. We'll come back with quantities, pricing and a delivery plan, usually within one working day.</p>
        </div>
        <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:28px; padding:clamp(20px,3vw,30px); display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,240px),1fr)); gap:14px">
          <input placeholder="Name" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F">
          <input placeholder="Company / Organization" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F">
          <input placeholder="Email" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F">
          <input placeholder="Phone" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F">
          <select style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F">
            <option>Event type</option><option>Corporate / office</option><option>Wedding</option><option>Party / celebration</option><option>Restaurant / café supply</option><option>Family gathering</option>
          </select>
          <input placeholder="Expected order date" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F">
          <input placeholder="Number of guests / quantity" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F">
          <input placeholder="Products interested in" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F">
          <select style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F">
            <option>Budget range</option><option>Under 10,000 tk</option><option>10,000 – 25,000 tk</option><option>25,000 – 50,000 tk</option><option>50,000 tk +</option>
          </select>
          <input placeholder="Delivery location" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F">
          <textarea rows="3" placeholder="Additional requirements" style="grid-column:1/-1; border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F; resize:vertical"></textarea>
          <button onClick="{{ submitBulk }}" style="grid-column:1/-1; justify-self:start; background:#F5AD18; color:#561530; border:0; border-radius:16px; padding:17px 30px; font-size:15px; font-weight:700; cursor:pointer">Submit Bulk Order Inquiry</button>
        </div>
      </sc-if>
    </section>
  </sc-if>
<?= $this->endSection() ?>
