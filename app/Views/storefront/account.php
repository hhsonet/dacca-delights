<?= $this->extend('storefront/layout') ?>
<?= $this->section('page') ?>
  <sc-if value="{{ isAccount }}" hint-placeholder-val="{{ false }}">
    <section style="max-width:1200px; margin:0 auto; padding:clamp(16px,3vw,36px) 16px clamp(36px,5vw,72px)">
      <div class="acct">
        <aside style="background:#561530; border-radius:24px; padding:20px; display:flex; flex-direction:column; gap:16px; position:sticky; top:88px">
          <div style="display:flex; align-items:center; gap:12px">
            <span style="width:48px; height:48px; border-radius:999px; background:#F5AD18; color:#561530; font-family:'Fraunces',serif; font-size:20px; display:flex; align-items:center; justify-content:center; flex:none">{{ acctInitials }}</span>
            <div style="display:flex; flex-direction:column; gap:2px; min-width:0">
              <span style="font-size:15px; font-weight:700; color:#FFF9F1">{{ acctFullName }}</span>
              <span style="font-size:11px; letter-spacing:0.14em; text-transform:uppercase; color:rgba(255,249,241,0.6)">Customer</span>
            </div>
          </div>
          <nav class="asbnav">
            <sc-for list="{{ accountNav }}" as="n" hint-placeholder-count="5">
              <button onClick="{{ n.select }}" style="flex:none; display:flex; align-items:center; gap:10px; text-align:left; border:0; border-radius:14px; padding:13px 14px; font-size:14px; font-weight:600; cursor:pointer; white-space:nowrap; transition:all 150ms ease; background:{{ n.bg }}; color:{{ n.fg }}">
                <span style="font-size:14px; opacity:0.9">{{ n.icon }}</span>
                <span>{{ n.name }}</span>
              </button>
            </sc-for>
          </nav>
          <div style="height:1px; background:rgba(255,249,241,0.18)"></div>
          <button onClick="{{ goMenu }}" style="background:#F5AD18; color:#561530; border:0; border-radius:14px; padding:14px; font-size:13px; font-weight:700; cursor:pointer">Order Now</button>
          <button onClick="{{ logout }}" style="background:none; border:1px solid rgba(255,249,241,0.3); color:#FFF9F1; border-radius:14px; padding:13px; font-size:13px; font-weight:600; cursor:pointer" style-hover="border-color:#F5AD18; color:#F5AD18">Logout</button>
        </aside>

        <div style="display:flex; flex-direction:column; gap:20px; min-width:0">

          <sc-if value="{{ tabDashboard }}" hint-placeholder-val="{{ true }}">
            <div style="display:flex; flex-wrap:wrap; gap:14px; justify-content:space-between; align-items:flex-end">
              <div style="display:flex; flex-direction:column; gap:6px">
                <h1 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(28px,5vw,42px); margin:0; color:#561530">Hello, {{ acctName }} 👋</h1>
                <p style="margin:0; font-size:14px; color:#75666B">Here's an overview of your recent bakery orders.</p>
              </div>
              <button onClick="{{ goMenu }}" style="background:#F5AD18; color:#561530; border:0; border-radius:16px; padding:14px 24px; font-size:13px; font-weight:700; cursor:pointer">Order Now</button>
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(min(46%,180px),1fr)); gap:12px">
              <sc-for list="{{ orderStats }}" as="st" hint-placeholder-count="4">
                <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:20px; padding:18px; display:flex; flex-direction:column; gap:6px">
                  <span style="font-size:11px; font-weight:700; letter-spacing:0.14em; text-transform:uppercase; color:#75666B">{{ st.label }}</span>
                  <span style="font-family:'Fraunces',serif; font-size:34px; color:{{ st.color }}; line-height:1">{{ st.value }}</span>
                </div>
              </sc-for>
            </div>
          </sc-if>

          <sc-if value="{{ tabOrders }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; flex-direction:column; gap:6px">
              <h1 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(28px,5vw,42px); margin:0; color:#561530">Your Orders</h1>
              <p style="margin:0; font-size:14px; color:#75666B">{{ orderCountLabel }}</p>
            </div>
          </sc-if>

          <sc-if value="{{ showOrderList }}" hint-placeholder-val="{{ true }}">
            <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; overflow:hidden">
              <div style="padding:18px 20px; border-bottom:1px solid #EADFE2; display:flex; justify-content:space-between; align-items:center; gap:12px">
                <span style="font-size:13px; font-weight:700; letter-spacing:0.14em; text-transform:uppercase; color:#561530">Recent Orders</span>
                <button onClick="{{ selectOrdersTab }}" style="background:none; border:0; cursor:pointer; font-size:13px; font-weight:600; color:#811844; padding:0">View all</button>
              </div>
              <div class="otable">
                <table style="width:100%; border-collapse:collapse">
                  <thead>
                    <tr style="background:#FFF9F1">
                      <sc-for list="{{ orderColumns }}" as="c" hint-placeholder-count="7">
                        <th style="text-align:left; padding:13px 16px; font-size:11px; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:#75666B; border-bottom:1px solid #EADFE2; white-space:nowrap">{{ c }}</th>
                      </sc-for>
                    </tr>
                  </thead>
                  <tbody>
                    <sc-for list="{{ orders }}" as="o" hint-placeholder-count="4">
                      <tr style="border-bottom:1px solid #EADFE2">
                        <td style="padding:15px 16px; font-size:14px; font-weight:700; color:#561530; white-space:nowrap">{{ o.no }}</td>
                        <td style="padding:15px 16px; font-size:14px; color:#75666B; white-space:nowrap">{{ o.date }}</td>
                        <td style="padding:15px 16px; font-size:14px; color:#2B171F">{{ o.itemsLabel }}</td>
                        <td style="padding:15px 16px; font-size:14px; font-weight:700; color:#561530; white-space:nowrap">{{ o.total }}</td>
                        <td style="padding:15px 16px; font-size:13px; color:#75666B; white-space:nowrap">{{ o.payment }}</td>
                        <td style="padding:15px 16px"><span style="display:inline-block; border-radius:999px; padding:6px 12px; font-size:11px; font-weight:700; white-space:nowrap; background:{{ o.statusBg }}; color:{{ o.statusFg }}">{{ o.status }}</span></td>
                        <td style="padding:15px 16px"><button onClick="{{ o.open }}" style="background:none; border:1px solid #EADFE2; border-radius:12px; padding:9px 14px; font-size:12px; font-weight:600; color:#561530; cursor:pointer; white-space:nowrap" style-hover="border-color:#9E1C60; color:#9E1C60">View</button></td>
                      </tr>
                    </sc-for>
                  </tbody>
                </table>
              </div>
              <div class="ocards" style="display:flex; flex-direction:column">
                <sc-for list="{{ orders }}" as="o" hint-placeholder-count="4">
                  <button onClick="{{ o.open }}" style="text-align:left; background:none; border:0; border-bottom:1px solid #EADFE2; padding:16px 18px; cursor:pointer; display:flex; flex-direction:column; gap:8px">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px">
                      <span style="font-size:15px; font-weight:700; color:#561530">{{ o.no }}</span>
                      <span style="border-radius:999px; padding:6px 12px; font-size:11px; font-weight:700; background:{{ o.statusBg }}; color:{{ o.statusFg }}">{{ o.status }}</span>
                    </div>
                    <span style="font-size:13px; color:#75666B">{{ o.date }} · {{ o.itemsLabel }} · {{ o.payment }}</span>
                    <span style="font-size:16px; font-weight:700; color:#2B171F">{{ o.total }}</span>
                  </button>
                </sc-for>
              </div>
            </div>
          </sc-if>

          <sc-if value="{{ tabProfile }}" hint-placeholder-val="{{ false }}">
            <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:clamp(20px,3vw,28px); display:flex; flex-direction:column; gap:22px">
              <div style="display:flex; flex-wrap:wrap; gap:18px; align-items:center">
                <span style="width:88px; height:88px; border-radius:999px; background:#F3E7D6; color:#811844; font-family:'Fraunces',serif; font-size:32px; display:flex; align-items:center; justify-content:center; flex:none">{{ acctInitials }}</span>
                <div style="display:flex; flex-direction:column; gap:4px; min-width:0">
                  <h1 style="font-family:'Fraunces',serif; font-weight:600; font-size:32px; margin:0; color:#561530">{{ acctFullName }}</h1>
                  <span style="font-size:14px; color:#75666B">{{ acctEmail }}</span>
                  <span style="font-size:12px; color:#9E1C60; font-weight:600">Member since March 2024</span>
                </div>
                <div style="display:flex; flex-direction:column; gap:6px; margin-left:auto">
                  <button onClick="{{ uploadPhoto }}" style="background:#561530; color:#FFF9F1; border:0; border-radius:14px; padding:13px 20px; font-size:13px; font-weight:600; cursor:pointer">Change Photo</button>
                  <span style="font-size:11px; color:#75666B">JPG, PNG or WEBP · max 2MB</span>
                </div>
              </div>
              <div style="height:1px; background:#EADFE2"></div>
              <div style="display:flex; flex-direction:column; gap:14px">
                <span style="font-size:11px; font-weight:700; letter-spacing:0.2em; color:#9E1C60">PERSONAL INFORMATION</span>
                <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,220px),1fr)); gap:14px">
                  <label style="display:flex; flex-direction:column; gap:7px; font-size:12px; font-weight:600; color:#75666B">Full name
                    <input value="{{ acctFullName }}" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:14px; font-size:15px; color:#2B171F; font-weight:400">
                  </label>
                  <label style="display:flex; flex-direction:column; gap:7px; font-size:12px; font-weight:600; color:#75666B">Email (read-only)
                    <input value="{{ acctEmail }}" readOnly="{{ true }}" style="border:1px solid #EADFE2; border-radius:14px; background:#F3ECE6; padding:14px; font-size:15px; color:#75666B; font-weight:400">
                  </label>
                  <label style="display:flex; flex-direction:column; gap:7px; font-size:12px; font-weight:600; color:#75666B">Phone number
                    <input value="{{ acctPhone }}" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:14px; font-size:15px; color:#2B171F; font-weight:400">
                  </label>
                </div>
                <button onClick="{{ saveProfile }}" style="align-self:flex-start; background:#F5AD18; color:#561530; border:0; border-radius:16px; padding:15px 28px; font-size:14px; font-weight:700; cursor:pointer">Save Changes</button>
              </div>
            </div>
          </sc-if>

          <sc-if value="{{ tabAddresses }}" hint-placeholder-val="{{ false }}">
            <div style="display:flex; flex-direction:column; gap:14px">
              <h1 style="font-family:'Fraunces',serif; font-weight:600; font-size:clamp(28px,5vw,40px); margin:0; color:#561530">Addresses</h1>
              <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,260px),1fr)); gap:14px">
                <sc-for list="{{ addresses }}" as="a" hint-placeholder-count="2">
                  <div style="background:#FFFFFF; border:1px solid {{ a.border }}; border-radius:20px; padding:20px; display:flex; flex-direction:column; gap:8px">
                    <div style="display:flex; align-items:center; gap:8px">
                      <span style="font-size:14px; font-weight:700; color:#561530">{{ a.label }}</span>
                      <sc-if value="{{ a.isDefault }}" hint-placeholder-val="{{ false }}"><span style="background:#F5AD18; color:#561530; border-radius:999px; padding:4px 10px; font-size:10px; font-weight:700">DEFAULT</span></sc-if>
                    </div>
                    <span style="font-size:14px; line-height:1.65; color:#75666B">{{ a.line }}</span>
                    <div style="display:flex; gap:14px; padding-top:4px">
                      <button onClick="{{ a.edit }}" style="background:none; border:0; padding:0; cursor:pointer; font-size:13px; font-weight:600; color:#811844">Edit</button>
                      <button onClick="{{ a.remove }}" style="background:none; border:0; padding:0; cursor:pointer; font-size:13px; font-weight:600; color:#75666B">Remove</button>
                    </div>
                  </div>
                </sc-for>
                <button onClick="{{ addAddress }}" style="background:none; border:1px dashed #EADFE2; border-radius:20px; padding:20px; cursor:pointer; font-size:14px; font-weight:600; color:#9E1C60; min-height:120px">+ Add new address</button>
              </div>
            </div>
          </sc-if>

          <sc-if value="{{ tabPassword }}" hint-placeholder-val="{{ false }}">
            <div style="background:#FFFFFF; border:1px solid #EADFE2; border-radius:24px; padding:clamp(20px,3vw,28px); display:flex; flex-direction:column; gap:14px; max-width:520px">
              <h1 style="font-family:'Fraunces',serif; font-weight:600; font-size:32px; margin:0; color:#561530">Change Password</h1>
              <input type="password" placeholder="Current password" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F">
              <input type="password" placeholder="New password" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F">
              <input type="password" placeholder="Confirm new password" style="border:1px solid #EADFE2; border-radius:14px; background:#FFF9F1; padding:15px; font-size:15px; color:#2B171F">
              <button onClick="{{ savePassword }}" style="align-self:flex-start; background:#F5AD18; color:#561530; border:0; border-radius:16px; padding:15px 28px; font-size:14px; font-weight:700; cursor:pointer">Update Password</button>
            </div>
          </sc-if>

        </div>
      </div>
    </section>
  </sc-if>
<?= $this->endSection() ?>
