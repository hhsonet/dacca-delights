<?= $this->extend('storefront/layout') ?>
<?= $this->section('page') ?>
  <sc-if value="{{ isProduct }}" hint-placeholder-val="{{ false }}">
    <section style="max-width:1200px; margin:0 auto; padding:clamp(16px,2vw,32px) 16px clamp(36px,5vw,72px)">
      <button onClick="{{ goMenu }}" style="background:none; border:0; padding:0 0 18px; cursor:pointer; font-size:13px; color:#75666B" style-hover="color:#561530">← Back to menu</button>
      <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:clamp(20px,4vw,48px)">
        <div style="display:flex; flex-direction:column; gap:12px">
          <div style="aspect-ratio:1/1; border-radius:28px; overflow:hidden; background:#F3E7D6">
            <img src="{{ detail.image }}" srcSet="{{ detail.imageSet }}" alt="{{ detail.name }}" loading="lazy" decoding="async" sizes="{{ detail.imageSizes }}" onError="{{ onImgError }}" style="width:100%; height:100%; object-fit:cover; display:block">
          </div>
          <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:10px">
            <sc-for list="{{ detail.thumbs }}" as="t" hint-placeholder-count="3">
              <button onClick="{{ t.select }}" aria-label="{{ t.alt }}" style="aspect-ratio:1/1; border-radius:14px; overflow:hidden; background:#F3E7D6; padding:0; cursor:pointer; display:block; border:2px solid {{ t.border }}; transition:border-color 160ms ease">
                <img src="{{ t.src }}" srcSet="{{ t.set }}" alt="{{ t.alt }}" loading="lazy" decoding="async" sizes="{{ t.sizes }}" onError="{{ onImgError }}" style="width:100%; height:100%; object-fit:cover; display:block">
              </button>
            </sc-for>
          </div>
        </div>
        <div style="display:flex; flex-direction:column; gap:16px">
          <span style="font-size:11px; font-weight:700; letter-spacing:0.22em; color:#9E1C60">{{ detail.categoryUpper }}</span>
          <h1 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(32px,6vw,52px); margin:0; color:#561530; line-height:1.05">{{ detail.name }}</h1>
          <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center">
            <span style="font-size:12px; font-weight:400; color:#6B7280">{{ detail.note }}</span>
            <span style="background:#FDF3DF; color:#8A5A08; border-radius:6px; padding:4px 9px; font-size:11px; font-weight:700; white-space:nowrap">{{ detail.kcal }}</span>
          </div>
          <sc-if value="{{ detail.hasMoqNote }}" hint-placeholder-val="{{ false }}">
            <span style="background:#FFF4DC; border-left:3px solid #F5AD18; border-radius:0 10px 10px 0; padding:10px 14px; font-size:12.5px; line-height:1.6; font-weight:600; color:#561530">{{ detail.moqNote }}</span>
          </sc-if>
          <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:16px; padding:15px 17px; display:flex; flex-direction:column; gap:5px">
            <span style="font-size:10.5px; font-weight:700; letter-spacing:0.16em; color:#9E1C60">KEY INGREDIENTS</span>
            <span style="font-size:13.5px; font-weight:400; line-height:1.6; color:#2B171F">{{ detail.ing }}</span>
            
          </div>
          <p style="margin:0; font-size:15px; line-height:1.75; color:#75666B; max-width:48ch; text-wrap:pretty">{{ detail.desc }}</p>
          <div style="display:flex; align-items:baseline; gap:12px; padding:16px 0; border-top:1px solid #EADFE2; border-bottom:1px solid #EADFE2">
            <span style="font-size:32px; font-weight:700; color:#561530">{{ detail.price }}</span>
            
          </div>
          <sc-if value="{{ hasOpts }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; flex-direction:column; gap:16px; padding-bottom:16px; border-bottom:1px solid #EADFE2">
            <sc-if value="{{ showFilling }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; flex-direction:column; gap:9px">
              <span style="font-size:11px; font-weight:700; letter-spacing:0.16em; color:#561530">FILLING <span style="color:#9E1C60">*</span></span>
              <div style="display:flex; flex-wrap:wrap; gap:9px">
                <sc-for list="{{ fillingOpts }}" as="o" hint-placeholder-count="2">
                  <button onClick="{{ o.select }}" style="display:flex; align-items:center; gap:9px; cursor:pointer; border-radius:999px; padding:11px 17px; font-size:13.5px; font-weight:600; transition:all 160ms ease; border:1.5px solid {{ o.border }}; background:{{ o.bg }}; color:{{ o.fg }}">
                    <span style="width:15px; height:15px; border-radius:999px; flex:none; border:1.5px solid {{ o.dotBorder }}; background:{{ o.dotBg }}"></span>
                    <span>{{ o.name }}</span>
                  </button>
                </sc-for>
              </div>
            </div>
            </sc-if>
            <sc-if value="{{ showSugar }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; flex-direction:column; gap:9px">
              <span style="font-size:11px; font-weight:700; letter-spacing:0.16em; color:#561530">SUGAR <span style="color:#9E1C60">*</span></span>
              <div style="display:flex; flex-wrap:wrap; gap:9px">
                <sc-for list="{{ sugarOpts }}" as="o" hint-placeholder-count="2">
                  <button onClick="{{ o.select }}" style="display:flex; align-items:center; gap:9px; cursor:pointer; border-radius:999px; padding:11px 17px; font-size:13.5px; font-weight:600; transition:all 160ms ease; border:1.5px solid {{ o.border }}; background:{{ o.bg }}; color:{{ o.fg }}">
                    <span style="width:15px; height:15px; border-radius:999px; flex:none; border:1.5px solid {{ o.dotBorder }}; background:{{ o.dotBg }}"></span>
                    <span>{{ o.name }}</span>
                  </button>
                </sc-for>
              </div>
            </div>
            </sc-if>
            <sc-if value="{{ showFormat }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; flex-direction:column; gap:9px">
              <span style="font-size:11px; font-weight:700; letter-spacing:0.16em; color:#561530">FORMAT <span style="color:#9E1C60">*</span></span>
              <div style="display:flex; flex-wrap:wrap; gap:9px">
                <sc-for list="{{ formOpts }}" as="o" hint-placeholder-count="2">
                  <button onClick="{{ o.select }}" style="display:flex; align-items:center; gap:9px; cursor:pointer; border-radius:999px; padding:11px 17px; font-size:13.5px; font-weight:600; transition:all 160ms ease; border:1.5px solid {{ o.border }}; background:{{ o.bg }}; color:{{ o.fg }}">
                    <span style="width:15px; height:15px; border-radius:999px; flex:none; border:1.5px solid {{ o.dotBorder }}; background:{{ o.dotBg }}"></span>
                    <span>{{ o.name }}</span>
                  </button>
                </sc-for>
              </div>
            </div>
              <sc-if value="{{ showSlice }}" hint-placeholder-val="{{ false }}">
                <div style="border-left:2px solid #F5AD18; padding-left:16px">
            <div style="display:flex; flex-direction:column; gap:9px">
              <span style="font-size:11px; font-weight:700; letter-spacing:0.16em; color:#561530">SLICE THICKNESS <span style="color:#9E1C60">*</span></span>
              <div style="display:flex; flex-wrap:wrap; gap:9px">
                <sc-for list="{{ sliceOpts }}" as="o" hint-placeholder-count="3">
                  <button onClick="{{ o.select }}" style="display:flex; align-items:center; gap:9px; cursor:pointer; border-radius:999px; padding:11px 17px; font-size:13.5px; font-weight:600; transition:all 160ms ease; border:1.5px solid {{ o.border }}; background:{{ o.bg }}; color:{{ o.fg }}">
                    <span style="width:15px; height:15px; border-radius:999px; flex:none; border:1.5px solid {{ o.dotBorder }}; background:{{ o.dotBg }}"></span>
                    <span>{{ o.name }}</span>
                  </button>
                </sc-for>
              </div>
            </div>
                </div>
              </sc-if>
            </sc-if>
            </div>
          </sc-if>

          <label style="display:flex; flex-direction:column; gap:7px; font-size:12px; font-weight:600; color:#75666B">Special instructions / notes
            <textarea rows="2" value="{{ notes }}" onChange="{{ onNotes }}" placeholder="Slice preference, message on the box, allergy note…" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:14px; font-size:15px; color:#2B171F; font-weight:400; resize:vertical"></textarea>
          </label>

          <div style="display:flex; flex-direction:column; gap:9px">
            <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center">
              <div style="display:flex; align-items:center; border:1px solid #EADFE2; border-radius:16px; background:#FFFFFF">
                <button onClick="{{ decQty }}" aria-label="Decrease" style="width:48px; height:52px; background:none; border:0; cursor:pointer; font-size:20px; color:#561530">−</button>
                <span style="min-width:36px; text-align:center; font-size:16px; font-weight:600">{{ qty }}</span>
                <button onClick="{{ incQty }}" aria-label="Increase" style="width:48px; height:52px; background:none; border:0; cursor:pointer; font-size:20px; color:#561530">+</button>
              </div>
              <button onClick="{{ addDetail }}" disabled="{{ addBlocked }}" style="flex:1 1 160px; background:{{ addBg }}; color:{{ addFg }}; border:0; border-radius:16px; padding:17px 24px; font-size:14px; font-weight:700; cursor:{{ addCursor }}; transition:background 160ms ease">Add to Cart</button>
              <button onClick="{{ buyDetail }}" disabled="{{ addBlocked }}" style="flex:1 1 160px; background:{{ buyBg }}; color:#561530; border:0; border-radius:16px; padding:17px 24px; font-size:14px; font-weight:700; cursor:{{ addCursor }}">Buy Now</button>
            </div>
            <sc-if value="{{ addBlocked }}" hint-placeholder-val="{{ false }}">
              <span style="font-size:12.5px; font-weight:600; color:#9E1C60">{{ addBlockedReason }}</span>
            </sc-if>
          </div>
          <ul style="margin:8px 0 0; padding:0; list-style:none; display:flex; flex-direction:column; gap:10px; font-size:13px; color:#75666B">
            <sc-for list="{{ detail.points }}" as="pt" hint-placeholder-count="3">
              <li style="display:flex; gap:10px; align-items:baseline"><span style="width:6px; height:6px; border-radius:999px; background:#F5AD18; flex:none"></span><span>{{ pt }}</span></li>
            </sc-for>
          </ul>
        </div>
      </div>
      <div style="margin-top:clamp(32px,5vw,64px)">
        <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(26px,4vw,38px); margin:0 0 20px; color:#561530">You might also like</h2>
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(min(100%,210px),1fr)); gap:16px">
          <sc-for list="{{ related }}" as="p" hint-placeholder-count="4">
            <article style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; overflow:hidden; display:flex; flex-direction:column">
              <button onClick="{{ p.open }}" style="border:0; padding:0; cursor:pointer; background:#F3E7D6; aspect-ratio:1/1; overflow:hidden; display:block">
                <img src="{{ p.image }}" alt="{{ p.name }}" loading="lazy" onError="{{ onImgError }}" style="width:100%; height:100%; object-fit:cover; display:block; transition:transform 420ms ease" style-hover="transform:scale(1.06)">
              </button>
              <div style="padding:14px; display:flex; flex-direction:column; gap:6px">
                <button onClick="{{ p.open }}" style="background:none; border:0; padding:0; text-align:left; cursor:pointer; font-family:'Fraunces',serif; font-weight:600; font-size:19px; line-height:1.28; letter-spacing:0.5px; color:#2B171F">{{ p.name }}</button>
                <span style="font-size:15px; font-weight:700; color:#561530">{{ p.price }}</span>
              </div>
            </article>
          </sc-for>
        </div>
      </div>
    </section>
  </sc-if>
<?= $this->endSection() ?>
