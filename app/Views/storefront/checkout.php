<?= $this->extend('storefront/layout') ?>
<?= $this->section('page') ?>
  <sc-if value="{{ isCheckout }}" hint-placeholder-val="{{ false }}">
    <section style="max-width:1100px; margin:0 auto; padding:clamp(20px,3vw,44px) 16px clamp(36px,5vw,72px)">
      <h1 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(32px,6vw,52px); margin:0 0 6px; color:#561530">Checkout</h1>
      <p style="margin:0 0 24px; font-size:14px; color:#75666B">Delivery details and payment. No account required.</p>
      <div style="display:flex; flex-wrap:wrap; gap:24px; align-items:flex-start">
        <div style="flex:3 1 380px; min-width:0; display:flex; flex-direction:column; gap:16px">
          <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:22px; display:flex; flex-direction:column; gap:14px">
            <span style="font-size:11px; font-weight:700; letter-spacing:0.2em; color:#9E1C60">FULFILMENT</span>
            <button onClick="{{ togglePickup }}" style="display:flex; align-items:flex-start; gap:12px; text-align:left; background:none; border:0; padding:0; cursor:pointer">
              <span style="width:22px; height:22px; border-radius:6px; flex:none; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; color:#561530; border:1.5px solid {{ pickupBorder }}; background:{{ pickupBg }}">{{ pickupMark }}</span>
              <span style="display:flex; flex-direction:column; gap:3px">
                <span style="font-size:15px; font-weight:600; color:#2B171F">Self-pickup from the kitchen</span>
                <span style="font-size:12.5px; line-height:1.55; color:#75666B">Collect your order yourself — no delivery fee.</span>
              </span>
            </button>
            <sc-if value="{{ pickup }}" hint-placeholder-val="{{ false }}">
              <p style="margin:0; border-left:2px solid #F5AD18; background:#FFF4DC; padding:12px 16px; font-size:13.5px; line-height:1.6; color:#561530">Pickup location: {{ pickupZone }}. We will notify you when your order is ready.</p>
            </sc-if>
          </div>

          <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:22px; display:flex; flex-direction:column; gap:14px">
            <span style="font-size:11px; font-weight:700; letter-spacing:0.2em; color:#9E1C60">DELIVERY DETAILS</span>
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,180px),1fr)); gap:12px">
              <label style="display:flex; flex-direction:column; gap:7px; font-size:12px; font-weight:600; color:#75666B"><span style="display:flex; align-items:baseline; gap:4px">First Name <span style="color: rgb(158, 28, 96);">*</span></span>
                <input value="{{ firstName }}" onChange="{{ onFirstName }}" placeholder="Hasan" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:14px; font-size:15px; color:#2B171F; font-weight:400">
              </label>
              <label style="display:flex; flex-direction:column; gap:7px; font-size:12px; font-weight:600; color:#75666B"><span style="display:flex; align-items:baseline; gap:4px">Last Name <span style="color: rgb(158, 28, 96);">*</span></span>
                <input value="{{ lastName }}" onChange="{{ onLastName }}" placeholder="Sonet" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:14px; font-size:15px; color:#2B171F; font-weight:400">
              </label>
            </div>
            <sc-if value="{{ nameError }}" hint-placeholder-val="{{ false }}">
              <span style="font-size:12px; font-weight:600; color:#9E1C60">{{ nameError }}</span>
            </sc-if>
            <div style="display:flex; flex-direction:column; gap:10px">
              <span style="font-size:12px; font-weight:600; color:#75666B">{{ dateLabel }}</span>
              <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,232px),1fr)); gap:14px">
                <sc-for list="{{ calendars }}" as="cal" hint-placeholder-count="2">
                  <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:16px; padding:14px; display:flex; flex-direction:column; gap:9px">
                    <span style="font-size:12.5px; font-weight:700; letter-spacing:0.06em; color:#561530; text-align:center">{{ cal.title }}</span>
                    <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:3px">
                      <sc-for list="{{ cal.dayNames }}" as="dn" hint-placeholder-count="7">
                        <span style="font-size:9.5px; font-weight:700; letter-spacing:0.04em; color:#A79A9E; text-align:center; padding-bottom:2px">{{ dn }}</span>
                      </sc-for>
                      <sc-for list="{{ cal.cells }}" as="c" hint-placeholder-count="35">
                        <button onClick="{{ c.select }}" disabled="{{ c.disabled }}" aria-label="{{ c.aria }}" style="aspect-ratio:1/1; display:flex; align-items:center; justify-content:center; border-radius:9px; font-size:12.5px; font-weight:{{ c.weight }}; cursor:{{ c.cursor }}; border:1px solid {{ c.border }}; background:{{ c.bg }}; color:{{ c.fg }}; transition:all 140ms ease">{{ c.day }}</button>
                      </sc-for>
                    </div>
                  </div>
                </sc-for>
              </div>
              <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:baseline; justify-content:space-between">
                <span style="font-size:13.5px; font-weight:700; color:{{ chosenDateColor }}">{{ chosenDateLabel }}</span>
                
              </div>
            </div>
            <sc-if value="{{ notPickup }}" hint-placeholder-val="{{ true }}">
              <label style="display:flex; flex-direction:column; gap:7px; font-size:12px; font-weight:600; color:#75666B">Delivery Area{{ zoneSuffix }}
                <select value="{{ zone }}" onChange="{{ onZone }}" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:14px; font-size:15px; color:#2B171F; font-weight:400">
                  <sc-for list="{{ zoneOptions }}" as="z" hint-placeholder-count="8">
                    <option value="{{ z.value }}">{{ z.label }}</option>
                  </sc-for>
                </select>
              </label>
              <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:baseline; justify-content:space-between">
                <span style="font-size:12.5px; color:#75666B">Delivery fee</span>
                <span style="font-size:14px; font-weight:700; color:#561530">{{ zoneFeeLabel }}</span>
              </div>
              <sc-if value="{{ zoneLimited }}" hint-placeholder-val="{{ false }}">
                <p style="margin:0; border-left:2px solid #9E1C60; background:#FBEDF3; padding:12px 16px; font-size:13px; line-height:1.6; color:#811844"><strong>Limited service.</strong> We deliver to {{ zone }} on selected days only, from {{ zoneFeeLabel }}. We'll confirm your slot on WhatsApp before baking.</p>
              </sc-if>
              <label style="display:flex; flex-direction:column; gap:7px; font-size:12px; font-weight:600; color:#75666B"><span style="display:flex; align-items:baseline; gap:4px">House Name/Number <span style="color:#9E1C60">*</span></span>
                <input value="{{ house }}" onChange="{{ onHouse }}" placeholder="House 42 / Bakul Villa" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:14px; font-size:15px; color:#2B171F; font-weight:400">
              </label>
              <label style="display:flex; flex-direction:column; gap:7px; font-size:12px; font-weight:600; color:#75666B"><span style="display:flex; align-items:baseline; gap:4px">Address Line 1 <span style="color:#9E1C60">*</span></span>
                <input value="{{ line1 }}" onChange="{{ onLine1 }}" placeholder="Road 11, Block D" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:14px; font-size:15px; color:#2B171F; font-weight:400">
              </label>
              <label style="display:flex; flex-direction:column; gap:7px; font-size:12px; font-weight:600; color:#75666B">Address Line 2
                <input value="{{ line2 }}" onChange="{{ onLine2 }}" placeholder="Flat 4B, near the mosque" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:14px; font-size:15px; color:#2B171F; font-weight:400">
              </label>
              <label style="display:flex; flex-direction:column; gap:7px; font-size:12px; font-weight:600; color:#75666B; max-width:180px">Zip Code
                <input value="{{ zip }}" onChange="{{ onZip }}" inputMode="numeric" maxLength="4" placeholder="1213" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:14px; font-size:15px; color:#2B171F; font-weight:400">
              </label>
              <sc-if value="{{ addressError }}" hint-placeholder-val="{{ false }}">
                <span style="font-size:12px; font-weight:600; color:#9E1C60">{{ addressError }}</span>
              </sc-if>



              <label style="display:flex; flex-direction:column; gap:7px; font-size:12px; font-weight:600; color:#75666B">Location
                <span style="display:flex; flex-wrap:wrap; gap:10px; align-items:center">
                  <button onClick="{{ captureLocation }}" style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:14px; padding:13px 20px; font-size:13px; font-weight:600; color:#561530; cursor:pointer" style-hover="border-color:#561530">Capture my location</button>
                  <sc-if value="{{ geoStatus }}" hint-placeholder-val="{{ false }}">
                    <span style="font-size:12.5px; font-weight:600; color:#811844">{{ geoStatus }}</span>
                  </sc-if>
                </span>
                <input value="{{ mapsUrl }}" onChange="{{ onMapsUrl }}" placeholder="Or paste Google Maps URL / detailed address instructions" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:14px; font-size:15px; color:#2B171F; font-weight:400">
              </label>
            </sc-if>
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,240px),1fr)); gap:12px">
              <label style="display:flex; flex-direction:column; gap:7px; font-size:12px; font-weight:600; color:#75666B"><span style="display:flex; align-items:baseline; gap:4px">Local Contact Number (for delivery rider) <span style="color:#9E1C60">*</span></span>
                <span style="display:flex; align-items:stretch">
                  <span style="display:flex; align-items:center; padding:0 13px; border:1px solid #EADFE2; border-right:0; border-radius:14px 0 0 14px; background:#F3ECE6; font-size:14px; font-weight:700; color:#561530">+880</span>
                  <input inputMode="numeric" maxLength="10" value="{{ localPhone }}" onChange="{{ onLocalPhone }}" placeholder="17XXXXXXXX" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:14px; font-size:15px; color:#2B171F; font-weight:400; border-radius:0 14px 14px 0; min-width:0; flex:1">
                </span>
                <sc-if value="{{ localPhoneError }}" hint-placeholder-val="{{ false }}">
                  <span style="font-size:12px; font-weight:600; color:#9E1C60">{{ localPhoneError }}</span>
                </sc-if>
              </label>

              <label style="display:flex; flex-direction:column; gap:7px; font-size:12px; font-weight:600; color:#75666B"><span style="display:flex; align-items:baseline; gap:4px">WhatsApp Number (for order confirmation) <span style="color:#9E1C60">*</span></span>
                <button onClick="{{ toggleWaSame }}" style="display:flex; align-items:center; gap:9px; background:none; border:0; padding:0; cursor:pointer; text-align:left">
                  <span style="width:18px; height:18px; border-radius:6px; flex:none; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:#561530; border:1.5px solid {{ waSameBorder }}; background:{{ waSameBg }}">{{ waSameMark }}</span>
                  <span style="font-size:13px; font-weight:500; color:#2B171F">Same as local number</span>
                </button>
                <span style="display:flex; align-items:stretch; gap:8px">
                  <input list="ddCountryCodes" value="{{ waCode }}" onChange="{{ onWaCode }}" disabled="{{ waSame }}" aria-label="Country code" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:14px; font-size:15px; color:#2B171F; font-weight:700; width:92px; flex:none; min-width:0">
                  <datalist id="ddCountryCodes">
                    <sc-for list="{{ countryCodes }}" as="cc" hint-placeholder-count="8">
                      <option value="{{ cc.code }}">{{ cc.label }}</option>
                    </sc-for>
                  </datalist>
                  <input inputMode="numeric" value="{{ waNumber }}" onChange="{{ onWaNumber }}" disabled="{{ waSame }}" placeholder="17XXXXXXXX" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:14px; font-size:15px; color:#2B171F; font-weight:400; min-width:0; flex:1">
                </span>
                <span style="font-size:12px; line-height:1.55; font-weight:500; color:#811844; border-left:2px solid #F5AD18; padding-left:11px">Please double-check your WhatsApp number. We will send your order details and final confirmation via WhatsApp before dispatching your baked goods.</span>
              </label>
            </div>
            <textarea rows="2" placeholder="Order notes (gate code, floor, message on the box…)" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:14px; font-size:15px; color:#2B171F; resize:vertical"></textarea>
          </div>
          <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:22px; display:flex; flex-direction:column; gap:10px">
            <span style="font-size:11px; font-weight:700; letter-spacing:0.2em; color:#9E1C60">PAYMENT METHOD <span style="color:#9E1C60">*</span></span>
            <sc-if value="{{ codHidden }}" hint-placeholder-val="{{ false }}">
              <p style="margin:0; font-size:12.5px; line-height:1.6; color:#75666B">{{ codHiddenNote }}</p>
            </sc-if>
            <sc-for list="{{ payments }}" as="pm" hint-placeholder-count="3">
              <button onClick="{{ pm.select }}" style="display:flex; align-items:center; justify-content:space-between; gap:12px; text-align:left; cursor:pointer; padding:16px; border-radius:16px; font-size:15px; color:#2B171F; border:1.5px solid {{ pm.border }}; background:{{ pm.bg }}; transition:all 160ms ease">
                <span style="font-weight:600">{{ pm.name }}</span>
                <span style="font-size:12px; color:#75666B">{{ pm.note }}</span>
              </button>
            </sc-for>
          </div>
        </div>
        <aside style="flex:1 1 280px; max-width:380px; position:sticky; top:88px; background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:22px; display:flex; flex-direction:column; gap:12px">
          <span style="font-size:11px; font-weight:700; letter-spacing:0.2em; color:#75666B">ORDER SUMMARY</span>
          <sc-for list="{{ cartLines }}" as="l" hint-placeholder-count="2">
            <div style="display:flex; gap:10px; align-items:center; padding-bottom:10px; border-bottom:1px solid #EADFE2">
              <span style="position:relative; flex:none; display:block"><img src="{{ l.image }}" alt="{{ l.name }}" loading="lazy" onError="{{ onImgError }}" style="width:40px; height:40px; border-radius:12px; object-fit:cover; flex:none"><sc-if value="{{ l.showOrigin }}" hint-placeholder-val="{{ false }}"><span title="{{ l.originTitle }}" style="position:absolute; right:-3px; bottom:-3px; width:16px; height:16px; border-radius:999px; border:2px solid #FFFFFF; background:{{ l.originBg }}; color:#FFFFFF; font-size:8.5px; font-weight:700; display:flex; align-items:center; justify-content:center; line-height:1">{{ l.originMark }}</span></sc-if></span>
              <span style="flex:1; min-width:0; display:flex; flex-direction:column; gap:2px"><span style="font-size:13px; color:#2B171F">{{ l.qtyName }}</span>
              <sc-if value="{{ l.hasOptions }}" hint-placeholder-val="{{ false }}">
                <span style="font-size:11.5px; font-weight:700; letter-spacing:0.04em; color:#9E1C60">{{ l.options }}</span>
              </sc-if>
              <sc-if value="{{ l.hasNote }}" hint-placeholder-val="{{ false }}">
                <span style="font-size:11.5px; line-height:1.5; color:#75666B">Note: {{ l.note }}</span>
              </sc-if></span>
              <span style="font-size:13px; font-weight:600; white-space:nowrap">{{ l.lineTotal }}</span>
            </div>
          </sc-for>
          <div style="display:flex; justify-content:space-between; font-size:14px; color:#75666B"><span>Subtotal</span><span>{{ subtotal }}</span></div>
          <sc-if value="{{ couponApplied }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; justify-content:space-between; font-size:14px; color:#811844"><span>Discount</span><span>−{{ discount }}</span></div>
          </sc-if>
          <div style="display:flex; justify-content:space-between; font-size:14px; color:#75666B"><span>Delivery</span><span>{{ deliveryLabel }}</span></div>
          <sc-if value="{{ moqWarn }}" hint-placeholder-val="{{ false }}">
            <div style="background:#FFF4DC; border-left:3px solid #F5AD18; border-radius:0 12px 12px 0; padding:13px 15px; display:flex; flex-direction:column; gap:7px; align-items:flex-start">
              <span style="font-size:13px; font-weight:700; color:#561530">{{ moqTitle }}</span>
              <span style="font-size:12.5px; line-height:1.6; color:#75666B">{{ moqBody }}</span>
              <button onClick="{{ moqFix }}" style="background:#561530; color:#FFF9F1; border:0; border-radius:10px; padding:9px 15px; font-size:12px; font-weight:600; cursor:pointer">{{ moqTitle }}</button>
            </div>
          </sc-if>
          <div style="display:flex; justify-content:space-between; align-items:baseline; padding-top:12px; border-top:1px solid #EADFE2"><span style="font-size:15px; font-weight:600">Total</span><span style="font-size:26px; font-weight:700; color:#561530">{{ total }}</span></div>
          <button onClick="{{ placeOrder }}" disabled="{{ orderBlocked }}" style="background:{{ placeBg }}; color:#561530; border:0; border-radius:16px; padding:17px; font-size:15px; font-weight:700; cursor:{{ placeCursor }}">Place Order</button>
          <sc-if value="{{ orderBlocked }}" hint-placeholder-val="{{ true }}">
            <span style="font-size:12px; line-height:1.5; font-weight:600; color:#9E1C60; text-align:center">{{ orderBlockedReason }}</span>
          </sc-if>
          <span style="font-size:11px; color:#75666B; text-align:center">Online payment gateway to be connected at launch.</span>
        </aside>
      </div>
    </section>
  </sc-if>
<?= $this->endSection() ?>
