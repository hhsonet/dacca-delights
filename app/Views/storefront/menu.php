<?= $this->extend('storefront/layout') ?>
<?= $this->section('page') ?>
  <sc-if value="{{ isMenu }}" hint-placeholder-val="{{ false }}">
    <section style="max-width:1200px; margin:0 auto; padding:clamp(18px,3vw,36px) 16px clamp(80px,8vw,72px)">
      <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:16px">
        <h1 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(30px,5.4vw,46px); margin:0; color:#561530">Our Menu</h1>
        <p style="margin:0; font-size:14px; color:#75666B">Freshly baked favorites, made for every occasion.</p>
      </div>

      <div class="msearch" style="position:relative; margin-bottom:14px">
        <div style="display:flex; align-items:center; gap:10px; background:#FFFFFF; border:1px solid #EADFE2; border-radius:16px; padding:0 14px">
          <span style="font-size:16px; color:#9E1C60">⌕</span>
          <input value="{{ query }}" onChange="{{ onQuery }}" placeholder="Search cakes, pastries, bread..." style="flex:1; min-width:0; border:0; background:none; padding:15px 0; font-size:15px; color:#2B171F">
          <sc-if value="{{ hasQuery }}" hint-placeholder-val="{{ false }}">
            <button onClick="{{ clearQuery }}" aria-label="Clear search" style="background:none; border:0; cursor:pointer; font-size:15px; color:#75666B; padding:8px">✕</button>
          </sc-if>
        </div>
        <sc-if value="{{ showSuggest }}" hint-placeholder-val="{{ false }}">
          <div style="position:absolute; left:0; right:0; top:100%; margin-top:8px; z-index:40; background:#FFFFFF; border:1px solid #EADFE2; border-radius:18px; overflow:hidden; box-shadow:0 18px 44px rgba(86,21,48,0.14)">
            <sc-for list="{{ suggestions }}" as="sg" hint-placeholder-count="4">
              <button onClick="{{ sg.open }}" style="width:100%; text-align:left; display:flex; align-items:center; gap:12px; background:none; border:0; border-bottom:1px solid #EADFE2; padding:11px 14px; cursor:pointer" style-hover="background:#FFF9F1">
                <span style="position:relative; flex:none; display:block"><img src="{{ sg.image }}" alt="{{ sg.name }}" loading="lazy" style="width:42px; height:42px; border-radius:11px; object-fit:cover; flex:none">
<sc-if value="{{ sg.showOrigin }}" hint-placeholder-val="{{ false }}"><span title="{{ sg.originTitle }}" style="position:absolute; right:-3px; bottom:-3px; width:15px; height:15px; border-radius:999px; border:2px solid #FFFFFF; background:{{ sg.originBg }}; color:#FFFFFF; font-size:8px; font-weight:700; display:flex; align-items:center; justify-content:center; line-height:1">{{ sg.originMark }}</span></sc-if></span>
                <span style="flex:1; min-width:0; display:flex; flex-direction:column; gap:2px">
                  <span style="font-size:14px; font-weight:600; color:#2B171F">{{ sg.name }}</span>
                  <span style="font-size:11.5px; color:#75666B">{{ sg.category }}</span>
                </span>
                <span style="font-size:14px; font-weight:700; color:#561530">{{ sg.price }}</span>
              </button>
            </sc-for>
          </div>
        </sc-if>
      </div>

      <div class="nb" style="position:sticky; top:72px; z-index:30; background:#FFF9F1; display:flex; gap:8px; overflow-x:auto; padding:8px 0 12px; margin-bottom:6px">
        <sc-for list="{{ categoryChips }}" as="c" hint-placeholder-count="8">
          <button onClick="{{ c.select }}" style="flex:none; border-radius:999px; padding:10px 18px; font-size:13.5px; font-weight:600; cursor:pointer; transition:all 160ms ease; border:1px solid {{ c.border }}; background:{{ c.bg }}; color:{{ c.fg }}">{{ c.name }}</button>
        </sc-for>
      </div>

      <div style="display:flex; align-items:baseline; justify-content:space-between; gap:12px; padding:6px 0 14px">
        <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(20px,3vw,26px); margin:0; color:#561530">{{ menuTitle }}</h2>
        <span style="font-size:13px; color:#75666B; white-space:nowrap">{{ resultLabel }}</span>
      </div>

      <div style="display:flex; flex-wrap:wrap; gap:22px; align-items:flex-start">
        <div style="flex:4 1 420px; min-width:0; display:flex; flex-direction:column; gap:16px">
          <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(min(47%,190px),1fr)); gap:14px">
            <sc-for list="{{ listed }}" as="p" hint-placeholder-count="8">
              <article style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:20px; overflow:hidden; display:flex; flex-direction:column; transition:transform 200ms ease, border-color 200ms ease" style-hover="transform:translateY(-3px); border-color:#9E1C60">
                <button onClick="{{ p.open }}" style="position:relative; display:block; border:0; padding:0; margin:0; cursor:pointer; background:#F3E7D6; aspect-ratio:1/1; overflow:hidden; width:100%">
                  <img src="{{ p.image }}" alt="{{ p.name }}" loading="lazy" onError="{{ onImgError }}" style="width:100%; height:100%; object-fit:cover; display:block; transition:transform 420ms ease" style-hover="transform:scale(1.07)">
                  <sc-if value="{{ p.badge }}" hint-placeholder-val="{{ false }}">
                    <span style="position:absolute; left:10px; top:10px; border-radius:999px; padding:5px 11px; font-size:10px; font-weight:700; letter-spacing:0.06em; background:{{ p.badgeBg }}; color:{{ p.badgeFg }}">{{ p.badge }}</span>
                  </sc-if>
                  <sc-if value="{{ p.showOrigin }}" hint-placeholder-val="{{ false }}">
                    <span title="{{ p.originTitle }}" style="position:absolute; right:10px; bottom:10px; border-radius:999px; padding:4px 9px; font-size:9.5px; font-weight:700; letter-spacing:0.04em; background:{{ p.originBg }}; color:#FFFFFF">{{ p.originLabel }}</span>
                  </sc-if>
                </button>
                <div style="padding:13px; display:flex; flex-direction:column; gap:5px; flex:1">
                  <button onClick="{{ p.open }}" style="background:none; border:0; padding:0; text-align:left; cursor:pointer; font-family:'Fraunces',serif; font-weight:600; font-size:17px; line-height:1.32; letter-spacing:0.5px; color:#2B171F" style-hover="color:#811844">{{ p.name }}</button>
                  <span style="font-size:12px; font-weight:400; line-height:1.5; color:#6B7280">{{ p.note }}</span>
                  <span style="font-size:11px; font-weight:400; line-height:1.5; color:#6B7280; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden"><span style="font-weight:600; color:#4B5563">Ingredients:</span> {{ p.ing }}</span>
                  <span style="align-self:flex-start; background:#FDF3DF; color:#8A5A08; border-radius:6px; padding:3px 7px; font-size:10px; font-weight:700; white-space:nowrap">{{ p.kcal }}</span>
                  <div style="margin-top:auto; padding-top:11px; display:flex; align-items:center; justify-content:space-between; gap:8px">
                    <span style="font-size:16px; font-weight:700; color:#561530; white-space:nowrap">{{ p.price }}</span>
                    <sc-if value="{{ p.inCart }}" hint-placeholder-val="{{ false }}">
                      <div style="display:flex; align-items:center; border:1px solid #F5AD18; border-radius:12px; background:#FFF9F1">
                        <button onClick="{{ p.dec }}" aria-label="Decrease" style="width:32px; height:34px; background:none; border:0; cursor:pointer; font-size:16px; color:#561530">−</button>
                        <span style="min-width:20px; text-align:center; font-size:13px; font-weight:700; color:#561530">{{ p.qty }}</span>
                        <button onClick="{{ p.add }}" aria-label="Increase" style="width:32px; height:34px; background:none; border:0; cursor:pointer; font-size:16px; color:#561530">+</button>
                      </div>
                    </sc-if>
                    <sc-if value="{{ p.notInCart }}" hint-placeholder-val="{{ true }}">
                      <button onClick="{{ p.add }}" style="background:#F5AD18; color:#561530; border:0; border-radius:12px; padding:9px 14px; font-size:12px; font-weight:700; cursor:pointer; white-space:nowrap; transition:all 160ms ease" style-hover="background:#9E1C60; color:#FFFFFF">{{ p.addLabel }}</button>
                    </sc-if>
                  </div>
                </div>
              </article>
            </sc-for>
          </div>
          <sc-if value="{{ hasMore }}" hint-placeholder-val="{{ false }}">
            <button onClick="{{ viewMore }}" style="align-self:center; background:#FFFFFF; border:1px solid #561530; border-radius:16px; padding:14px 34px; font-size:14px; font-weight:700; color:#561530; cursor:pointer; transition:all 160ms ease" style-hover="background:#561530; color:#FFF9F1">View More</button>
          </sc-if>
          <sc-if value="{{ noResults }}" hint-placeholder-val="{{ false }}">
            <div style="background:#FFFFFF; border:1px dashed #EADFE2; border-radius:24px; padding:52px 24px; text-align:center; display:flex; flex-direction:column; gap:10px; align-items:center">
              <span style="font-family:'Fraunces',serif; font-size:26px; color:#561530">Nothing delicious found</span>
              <span style="font-size:14px; color:#75666B">Try another search or explore our best sellers.</span>
              <button onClick="{{ browseBest }}" style="margin-top:6px; background:#F5AD18; color:#561530; border:0; border-radius:16px; padding:14px 26px; font-size:13px; font-weight:700; cursor:pointer">Browse Best Sellers</button>
            </div>
          </sc-if>
        </div>
        <aside class="dnav" style="flex:1 1 240px; max-width:320px; position:sticky; top:140px; background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:20px; flex-direction:column; gap:14px">
          <span style="font-size:11px; font-weight:700; letter-spacing:0.2em; color:#75666B">YOUR CART</span>
          <sc-for list="{{ cartLines }}" as="l" hint-placeholder-count="2">
            <div style="display:flex; gap:10px; align-items:center; padding-bottom:12px; border-bottom:1px solid #EADFE2">
              <img src="{{ l.image }}" alt="{{ l.name }}" loading="lazy" style="width:40px; height:40px; border-radius:11px; object-fit:cover; flex:none">
              <span style="flex:1; min-width:0; display:flex; flex-direction:column; gap:2px"><span style="font-size:13px; color:#2B171F">{{ l.qtyName }}</span>
              <sc-if value="{{ l.hasOptions }}" hint-placeholder-val="{{ false }}">
                <span style="font-size:11.5px; font-weight:700; letter-spacing:0.04em; color:#9E1C60">{{ l.options }}</span>
              </sc-if>
              <sc-if value="{{ l.hasNote }}" hint-placeholder-val="{{ false }}">
                <span style="font-size:11.5px; line-height:1.5; color:#75666B">Note: {{ l.note }}</span>
              </sc-if></span>
              <span style="font-size:13px; font-weight:700; color:#561530; white-space:nowrap">{{ l.lineTotal }}</span>
            </div>
          </sc-for>
          <sc-if value="{{ cartEmpty }}" hint-placeholder-val="{{ true }}"><span style="font-size:13px; color:#75666B">Nothing added yet.</span></sc-if>
          <div style="display:flex; justify-content:space-between; align-items:baseline"><span style="font-size:14px; color:#75666B">Subtotal</span><span style="font-size:20px; font-weight:700; color:#561530">{{ subtotal }}</span></div>
          <button onClick="{{ goCart }}" style="background:#F5AD18; color:#561530; border:0; border-radius:16px; padding:15px; font-size:14px; font-weight:700; cursor:pointer; transition:background 160ms ease" style-hover="background:#9E1C60; color:#FFFFFF">View Cart</button>
        </aside>
      </div>
    </section>
  </sc-if>
<?= $this->endSection() ?>
