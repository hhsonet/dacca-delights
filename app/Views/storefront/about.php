<?= $this->extend('storefront/layout') ?>
<?= $this->section('page') ?>
  <sc-if value="{{ isAbout }}" hint-placeholder-val="{{ false }}">
    <section style="max-width:1200px; margin:0 auto; padding:clamp(20px,4vw,56px) 16px clamp(32px,4vw,64px); display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:clamp(24px,4vw,52px); align-items:center">
      <div style="display:flex; flex-direction:column; gap:16px">
        <span style="font-size:11px; font-weight:700; letter-spacing:0.24em; color:#9E1C60">OUR STORY</span>
        <h1 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(34px,6vw,58px); margin:0; color:#561530; line-height:1.03">Started small. Now rising like fresh dough in Dhaka.</h1>
        <p style="margin:0; font-size:15px; line-height:1.75; color:#75666B; text-wrap:pretty">Dacca Delights began it's journey small, with a notebook of WhatsApp and Facebook Messenger orders, and a simple belief: good baking takes time. Today, we’re a cloud kitchen in Dhaka, still keeping things small and intentional. We bake in batches, work with dough and recipes from scratch, and prepare what’s ordered, so every bake reaches you at its freshest.</p>
        <p style="margin:0; font-size:15px; line-height:1.75; color:#75666B; text-wrap:pretty">Our menu is deliberately focused: breads, pastries, muffins, cookies and desserts, each made in-house with the time and care it deserves.  No storefront. No unnecessary batches. Just fresh baking, made for your order and delivered to your door.</p>
      </div>
      <div style="aspect-ratio:4/5; border-radius:28px; overflow:hidden; background:#F3E7D6">
        <img src="{{ aboutImage }}" alt="Baker at work" loading="lazy" onError="{{ onImgError }}" style="width:100%; height:100%; object-fit:cover; display:block">
      </div>
    </section>
    <section style="max-width:1200px; margin:0 auto; padding:0 16px clamp(36px,5vw,72px)">
      <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,220px),1fr)); gap:16px">
        <sc-for list="{{ contactCards }}" as="c" hint-placeholder-count="4">
          <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:22px; display:flex; flex-direction:column; gap:8px">
            <span style="font-size:11px; font-weight:700; letter-spacing:0.18em; color:#9E1C60">{{ c.label }}</span><span style="font-size:15px; line-height:1.65; color:#2B171F">{{ c.value }}</span>
            
          </div>
        </sc-for>
      </div>
    </section>
  </sc-if>
<?= $this->endSection() ?>
