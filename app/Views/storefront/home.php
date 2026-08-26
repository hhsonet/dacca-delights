<?= $this->extend('storefront/layout') ?>
<?= $this->section('page') ?>
  <sc-if value="{{ isHome }}" hint-placeholder-val="{{ true }}">
    <section style="max-width: 1200px; margin: 0 auto; padding: clamp(20px,4vw,56px) 16px clamp(28px,4vw,64px); display: grid; grid-template-columns: repeat(auto-fit,minmax(300px,1fr)); gap: clamp(24px,4vw,52px); align-items: center; position: relative">
      <div style="display:flex; flex-direction:column; gap:18px; order:1">
        <span style="font-size:11px; font-weight:700; letter-spacing:0.24em; color:#9E1C60">BAKED FRESH DAILY</span>
        <h1 style="font-family: 'Fraunces',serif; font-weight: 700; font-size: clamp(42px,8vw,74px); line-height: 1.02; margin: 0; color: #561530; letter-spacing: -0.01em; text-wrap: balance; text-decoration-line: none; text-align: left">Pre-Order Today. Enjoy It Fresh.</h1>
        <p style="margin:0; font-size:16px; line-height:1.7; font-weight:500; color:#2C0E17; max-width:46ch; text-wrap:pretty">We’re a Dhaka-based cloud kitchen, baking in small batches and delivering freshly baked breads, pastries, cakes and desserts to your door.</p>
        <div style="display:flex; flex-wrap:wrap; gap:12px; padding-top:4px">
          <button onClick="{{ goMenu }}" style="background: #F5AD18; color: #561530; border: 0; border-radius: 18px; padding: 17px 30px; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: 0 10px 26px rgba(245,173,24,0.34); transition: transform 160ms ease, box-shadow 160ms ease; height: 50px; text-transform: capitalize" style-hover="transform:translateY(-2px); box-shadow:0 16px 34px rgba(245,173,24,0.42)">Pre-Order Now</button>
          <button onClick="{{ goMenu }}" style="background: #FFFFFF; color: #561530; border: 1px solid #EADFE2; border-radius: 18px; padding: 17px 28px; font-size: 13px; font-weight: 600; cursor: pointer; transition: border-color 160ms ease; height: 50px; text-transform: capitalize" style-hover="border-color:#561530">Explore Menu</button>
        </div>
      </div>
      <div style="position:relative; order:0">
        <div style="position:absolute; inset:-5% -3% 5% -7%; background:#9E1C60; opacity:0.10; border-radius:50% 50% 46% 54% / 54% 46% 50% 50%"></div>
        <div style="position:relative; aspect-ratio:1/1; border-radius:50% 50% 44% 56% / 56% 44% 50% 50%; overflow:hidden; background:#F3E7D6">
          <img src="https://www.daccadelights.com/assets/Items/croissants.jpeg" alt="Freshly baked croissants" style="width:100%; height:100%; object-fit:cover; display:block">
        </div>
        
        
      </div>
    </section>

    <section style="max-width:1200px; margin:0 auto; padding:0 0 clamp(24px,3vw,40px)">
      <div class="nb" style="display:flex; gap:10px; overflow-x:auto; padding:4px 16px; scroll-snap-type:x mandatory">
        <sc-for list="{{ categoryChips }}" as="c" hint-placeholder-count="7">
          <button onClick="{{ c.select }}" style="scroll-snap-align:start; flex:none; border-radius:999px; padding:11px 20px; font-size:14px; font-weight:600; cursor:pointer; transition:all 160ms ease; border:1px solid {{ c.border }}; background:{{ c.bg }}; color:{{ c.fg }}">{{ c.name }}</button>
        </sc-for>
      </div>
    </section>

    <section style="max-width:1200px; margin:0 auto; padding:0 16px clamp(32px,4vw,64px)">
      <div style="display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin-bottom:22px">
        <div style="display:flex; flex-direction:column; gap:6px">
          <span style="font-size:11px; font-weight:700; letter-spacing:0.24em; color:#9E1C60">FEATURED</span>
          <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(30px,5vw,46px); margin:0; color:#561530">Fresh From The Oven</h2>
        </div>
        <button onClick="{{ goMenu }}" style="background:none; border:0; cursor:pointer; font-size:14px; font-weight:600; color:#811844; padding:0; white-space:nowrap">View all →</button>
      </div>
      <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(min(100%,240px),1fr)); gap:16px">
        <sc-for list="{{ featured }}" as="p" hint-placeholder-count="8">
          <article style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; overflow:hidden; display:flex; flex-direction:column; transition:transform 220ms ease, box-shadow 220ms ease" style-hover="transform:translateY(-4px); box-shadow:0 18px 42px rgba(86,21,48,0.14)">
            <button onClick="{{ p.open }}" style="position:relative; display:block; border:0; padding:0; cursor:pointer; background:#F3E7D6; aspect-ratio:1/1; overflow:hidden">
              <img src="{{ p.image }}" alt="{{ p.name }}" loading="lazy" onError="{{ onImgError }}" style="width:100%; height:100%; object-fit:cover; display:block; transition:transform 420ms ease" style-hover="transform:scale(1.06)">
              <sc-if value="{{ p.isNew }}" hint-placeholder-val="{{ false }}">
                <span style="position:absolute; left:12px; top:12px; background:#9E1C60; color:#FFFFFF; font-size:11px; font-weight:700; border-radius:999px; padding:6px 12px">New</span>
              </sc-if>
            </button>
            <div style="padding:16px; display:flex; flex-direction:column; gap:8px; flex:1">
              <span style="font-size:11px; font-weight:600; letter-spacing:0.12em; text-transform:uppercase; color:#9E1C60">{{ p.category }}</span>
              <button onClick="{{ p.open }}" style="background:none; border:0; padding:0; text-align:left; cursor:pointer; font-family:'Fraunces',serif; font-weight:600; font-size:21px; line-height:1.24; letter-spacing:0.5px; color:#2B171F">{{ p.name }}</button>
              <p style="margin:0; font-size:12px; font-weight:400; line-height:1.6; color:#6B7280">{{ p.note }}</p>
              <p style="margin:0; font-size:11.5px; font-weight:400; line-height:1.55; color:#6B7280"><span style="font-weight:600; color:#4B5563">Key ingredients:</span> {{ p.ing }}</p>
              <span style="align-self:flex-start; background:#FDF3DF; color:#8A5A08; border-radius:6px; padding:3px 7px; font-size:10.5px; font-weight:700; white-space:nowrap">{{ p.kcal }}</span>
              <div style="margin-top:auto; padding-top:12px; display:flex; align-items:center; justify-content:space-between; gap:10px">
                <span style="font-size:18px; font-weight:700; color:#561530">{{ p.price }}</span>
                <button onClick="{{ p.add }}" aria-label="{{ p.addAria }}" style="min-width: 42px; height: 42px; padding: 0 {{ p.addPadX }}; border-radius: 14px; border: 1px solid #EADFE2; background: #FFF9F1; color: #561530; font-size: {{ p.addSize }}; font-weight: 700; cursor: pointer; flex: none; transition: all 180ms ease; text-align: center" style-hover="background:#F5AD18; border-color:#F5AD18">{{ p.addGlyph }}</button>
              </div>
            </div>
          </article>
        </sc-for>
      </div>
    </section>

    <section style="background:#561530; color:#FFF9F1">
      <div style="max-width:1200px; margin:0 auto; padding:clamp(28px,4vw,64px) 16px; display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:clamp(24px,4vw,52px); align-items:center">
        <div style="position:relative; aspect-ratio:16/9; border-radius:28px; overflow:hidden; background:#6B2440">
          <img src="{{ signatureImage }}" alt="Sourdough bread" loading="lazy" onError="{{ onImgError }}" style="width:100%; height:100%; object-fit:cover; display:block">
        </div>
        <div style="display:flex; flex-direction:column; gap:16px">
          <span style="font-size:11px; font-weight:700; letter-spacing:0.24em; color:#F5AD18">SIGNATURE</span>
          <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(32px,5vw,50px); margin:0; line-height:1.05">Sourdough Bread</h2>
          <p style="margin:0; font-size:15px; line-height:1.75; color:rgba(255,249,241,0.78); max-width:44ch; text-wrap:pretty">A 36-hour cold ferment on our own starter. Deep caramel crust, open crumb, faintly sour. Also in whole wheat, multigrain and olive cheese.</p>
          <div style="display:flex; align-items:baseline; gap:12px"><span style="font-size:30px; font-weight:700; color:#F5AD18">350 tk</span><span style="font-size:14px; color:rgba(255,249,241,0.6)">~650 gm loaf</span></div>
          <div style="display:flex; gap:12px; flex-wrap:wrap">
            <button onClick="{{ openSignature }}" style="background:#F5AD18; color:#561530; border:0; border-radius:18px; padding:16px 28px; font-size:14px; font-weight:700; cursor:pointer; transition:transform 160ms ease" style-hover="transform:translateY(-2px)">Order This Loaf</button>
            <button onClick="{{ goCakes }}" style="background:none; color:#FFF9F1; border:1px solid rgba(255,249,241,0.35); border-radius:18px; padding:16px 26px; font-size:14px; font-weight:600; cursor:pointer" style-hover="border-color:#F5AD18; color:#F5AD18">All Breads</button>
          </div>
        </div>
      </div>
    </section>

    <section style="max-width:1200px; margin:0 auto; padding:clamp(32px,4vw,64px) 16px">
      <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,220px),1fr)); gap:16px">
        <sc-for list="{{ whyUs }}" as="w" hint-placeholder-count="4">
          <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:24px; display:flex; flex-direction:column; gap:12px">
            <span style="width:44px; height:44px; border-radius:16px; border:1.5px solid #F5AD18; display:flex; align-items:center; justify-content:center; font-size:17px; color:#9E1C60">{{ w.icon }}</span>
            <span style="font-family:'Fraunces',serif; font-size:22px; color:#561530">{{ w.title }}</span>
            <span style="font-size:13px; line-height:1.65; color:#75666B">{{ w.body }}</span>
          </div>
        </sc-for>
      </div>
    </section>

    <section style="max-width:1200px; margin:0 auto; padding:0 16px clamp(32px,4vw,64px); display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:clamp(24px,4vw,52px); align-items:center">
      <div style="position:relative; aspect-ratio:4/5; border-radius:28px; overflow:hidden; background:#F3E7D6">
        <img src="{{ aboutImage }}" alt="Baker shaping dough" loading="lazy" onError="{{ onImgError }}" style="width:100%; height:100%; object-fit:cover; display:block">
      </div>
      <div style="display:flex; flex-direction:column; gap:16px">
        <span style="font-size:11px; font-weight:700; letter-spacing:0.24em; color:#9E1C60">OUR STORY</span>
        <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(32px,5vw,50px); margin:0; color:#561530; line-height:1.05">Started small. Now rising like fresh dough in Dhaka.</h2>
        <div style="margin:0; font-size:15px; line-height:1.75; color:#75666B; max-width:46ch; text-wrap:pretty"><div>Our menu is deliberately focused: breads, pastries, muffins, cookies and desserts, each made in-house with the time and care it deserves.</div><div><br></div><div>No storefront. No unnecessary batches. Just fresh baking, made for your order and delivered to your door.</div></div>
        <button onClick="{{ goAbout }}" style="align-self:flex-start; background:#FFFFFF; border:1px solid #EADFE2; border-radius:18px; padding:15px 26px; font-size:14px; font-weight:600; color:#561530; cursor:pointer" style-hover="border-color:#561530">Discover Our Story</button>
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; padding-top:16px; border-top:1px solid #EADFE2">
          <sc-for list="{{ stats }}" as="s" hint-placeholder-count="3">
            <div style="display:flex; flex-direction:column; gap:2px">
              
              
            </div>
          </sc-for>
        </div>
      </div>
    </section>

    <section style="max-width:1200px; margin:0 auto; padding:0 16px clamp(32px,4vw,64px)">
      <div style="background:#561530; border-radius:28px; overflow:hidden; display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,300px),1fr))">
        <div style="min-height:260px; position:relative; background:#6B2440">
          <img src="https://www.daccadelights.com/assets/Items/wraps.jpeg" alt="Bakery platters prepared for an event" loading="lazy" style="width:100%; height:100%; object-fit:cover; display:block; position:absolute; inset:0">
        </div>
        <div style="padding:clamp(26px,4vw,48px); display:flex; flex-direction:column; gap:18px; color:#FFF9F1">
          <span style="font-size:11px; font-weight:700; letter-spacing:0.24em; color:#F5AD18">BULK &amp; WHOLESALE</span>
          <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(30px,4.6vw,46px); margin:0; line-height:1.06">Planning a Big Order?</h2>
          <p style="margin:0; font-size:15px; line-height:1.75; color:rgba(255,249,241,0.78); max-width:48ch; text-wrap:pretty">From office events and corporate gatherings to weddings and celebrations, we prepare fresh bakery items in bulk. Contact us for custom quantities, pricing, and delivery options.</p>
          <div style="display:flex; flex-wrap:wrap; gap:8px">
            <sc-for list="{{ bulkHighlights }}" as="h" hint-placeholder-count="4">
              <span style="border:1px solid rgba(245,173,24,0.45); color:#F5AD18; border-radius:999px; padding:8px 14px; font-size:12px; font-weight:600">{{ h }}</span>
            </sc-for>
          </div>
          <div style="display:flex; flex-wrap:wrap; gap:12px; padding-top:4px">
            <button onClick="{{ goBulk }}" style="background:#F5AD18; color:#561530; border:0; border-radius:18px; padding:16px 28px; font-size:14px; font-weight:700; cursor:pointer; transition:transform 160ms ease" style-hover="transform:translateY(-2px)">Contact Us for Bulk Order</button>
            <a href="tel:+8801622823269" style="background:none; color:#FFF9F1; border:1px solid #9E1C60; border-radius:18px; padding:16px 26px; font-size:14px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center" style-hover="background:#9E1C60; color:#FFFFFF" target="_blank" rel="noopener noreferrer">Call Us</a>
          </div>
        </div>
      </div>
    </section>

    

    <section style="max-width:1200px; margin:0 auto; padding:0 16px clamp(32px,4vw,64px)">
      <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(30px,5vw,46px); margin:0 0 22px; color:#561530">Loved in Dhaka</h2>
      <div class="nb" style="display:grid; grid-auto-flow:column; grid-auto-columns:minmax(min(84vw,320px),340px); gap:16px; overflow-x:auto; scroll-snap-type:x mandatory; padding-bottom:6px">
        <sc-for list="{{ testimonials }}" as="t" hint-placeholder-count="5">
          <figure style="scroll-snap-align:start; margin:0; background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:24px; display:flex; flex-direction:column; gap:14px">
            <div style="display:flex; align-items:center; gap:12px">
              <span style="width:44px; height:44px; border-radius:999px; background:#F3E7D6; display:flex; align-items:center; justify-content:center; font-family:'Fraunces',serif; font-size:18px; color:#811844">{{ t.initial }}</span>
              <div style="display:flex; flex-direction:column; gap:2px">
                <span style="font-size:14px; font-weight:600; color:#2B171F">{{ t.name }}</span>
                <span style="color:#F5AD18; font-size:12px; letter-spacing:0.08em">{{ t.stars }}</span>
              </div>
            </div>
            <blockquote style="margin:0; font-size:14px; line-height:1.7; color:#75666B">{{ t.quote }}</blockquote>
            
          </figure>
        </sc-for>
      </div>
    </section>

    <section style="max-width:1200px; margin:0 auto; padding:0 16px clamp(36px,5vw,72px)">
      <div style="display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin-bottom:18px; flex-wrap:wrap">
        <h2 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(28px,4.4vw,42px); margin:0; color:#561530">#MadeWithLoaf</h2>
        <a href="https://instagram.com" style="font-size:14px; font-weight:600">Follow Our Sweet Journey →</a>
      </div>
      <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(min(46%,190px),1fr)); grid-auto-rows:130px; gap:12px">
        <sc-for list="{{ gallery }}" as="g" hint-placeholder-count="6">
          <div style="grid-row:span {{ g.span }}; border-radius:20px; overflow:hidden; background:#F3E7D6">
            <img src="{{ g.src }}" alt="{{ g.alt }}" loading="lazy" onError="{{ onImgError }}" style="width:100%; height:100%; object-fit:cover; display:block; transition:transform 420ms ease" style-hover="transform:scale(1.05)">
          </div>
        </sc-for>
      </div>
    </section>
  </sc-if>
<?= $this->endSection() ?>
