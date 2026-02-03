const form = document.getElementById("signatureForm");
const previewArea = document.getElementById("previewArea");
const htmlOutput = document.getElementById("htmlOutput");
const generateBtn = document.getElementById("generateBtn");
const copyBtn = document.getElementById("copyBtn");
const saveBtn = document.getElementById("saveBtn");
const deleteBtn = document.getElementById("deleteBtn");
const exportBtn = document.getElementById("exportBtn");
const savedList = document.getElementById("savedList");

const defaults = {
  fullName: "Michael Doe",
  pronouns: "(them • their • theirs)",
  roleLine: "Student | Example School",
  deptLine: "School of Higher Education",
  mobile: "723-364-380",
  telephone: "504-899-7214",
  email: "m.doe@example.edu",
  website: "example.edu",
  accent: "#2f60ff",
  theme: "campus",
  publications: "",
  linkedin: "",
  x: "",
  facebook: "",
  instagram: "",
  github: "",
  youtube: "",
};

const socialConfig = [
  { key: "facebook", label: "f" },
  { key: "linkedin", label: "in" },
  { key: "x", label: "x" },
  { key: "github", label: "gh" },
  { key: "instagram", label: "ig" },
  { key: "youtube", label: "yt" },
];

function setDefaults() {
  Object.entries(defaults).forEach(([key, value]) => {
    const field = form.elements[key];
    if (field && !field.value) {
      field.value = value;
    }
  });
}

function escapeHtml(value) {
  return value
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/\"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

function themePalette(theme, accent) {
  switch (theme) {
    case "mono":
      return { accent: "#1f1f1f", accent2: "#1f1f1f" };
    case "teal":
      return { accent: "#2dd4bf", accent2: "#0f766e" };
    default:
      return { accent: accent || "#2f60ff", accent2: "#5bd1ff" };
  }
}

function renderSocialLinks(values, palette) {
  return socialConfig
    .filter(({ key }) => values[key])
    .map(({ key, label }) => {
      const url = escapeHtml(values[key]);
      return `
        <a href="${url}" style="display:inline-block; width:22px; height:22px; border-radius:50%; background:${palette.accent}; color:#ffffff; font-size:11px; line-height:22px; text-align:center; text-decoration:none; margin-left:6px;">
          ${label}
        </a>
      `;
    })
    .join("");
}

function normalizeValues(formData) {
  const values = {};
  for (const [key, value] of formData.entries()) {
    if (value instanceof File) {
      values[key] = "";
    } else {
      values[key] = value;
    }
  }
  return values;
}

function currentId() {
  return form.elements.signatureId?.value || "";
}

function buildSignature(values) {
  const safe = Object.fromEntries(
    Object.entries(values).map(([key, value]) => [key, escapeHtml((value || "").trim())])
  );
  const palette = themePalette(values.theme, values.accent);

  const photoSrc = values.photoUrl || "";
  const bannerSrc = values.bannerUrl || "";

  const photoBlock = photoSrc
    ? `<img src="${escapeHtml(photoSrc)}" alt="${safe.fullName}" style="width:72px; height:72px; border-radius:14px; object-fit:cover; border:2px solid #f1f1f1;" />`
    : "";

  const bannerBlock = bannerSrc
    ? `<div style="margin-top:14px; border-radius:8px; overflow:hidden;">
        <img src="${escapeHtml(bannerSrc)}" alt="Banner" style="width:100%; display:block;" />
      </div>`
    : "";

  const pronouns = safe.pronouns ? `<div style="font-size:12px; color:#5c6370;">${safe.pronouns}</div>` : "";

  const publications = safe.publications
    ? `<a href="${safe.publications}" style="display:inline-flex; align-items:center; gap:8px; font-size:12px; color:${palette.accent}; text-decoration:none;">
        <span style="width:16px; height:16px; border-radius:6px; background:${palette.accent}; display:inline-block;"></span>
        My publications
      </a>`
    : "";

  const socials = renderSocialLinks(values, palette);

  return `
<table cellpadding="0" cellspacing="0" style="font-family:Arial, sans-serif; color:#1b1d23;">
  <tr>
    <td style="padding:0 16px 0 0; vertical-align:top;">
      ${photoBlock}
    </td>
    <td style="vertical-align:top;">
      <div style="font-weight:700; font-size:16px; color:${palette.accent};">${safe.fullName}</div>
      ${pronouns}
      <div style="font-size:13px; margin-top:6px; color:#1b1d23;">${safe.roleLine}</div>
      <div style="font-size:13px; color:#1b1d23;">${safe.deptLine}</div>
      <div style="margin-top:8px; font-size:12px; color:#4a4f5b;">
        <span style="margin-right:10px;">mobile: ${safe.mobile}</span>
        <span>telephone: ${safe.telephone}</span>
      </div>
      <div style="font-size:12px; color:#4a4f5b;">
        email: ${safe.email}${safe.website ? ` | ${safe.website}` : ""}
      </div>
      <table cellpadding="0" cellspacing="0" style="margin-top:10px; width:100%;">
        <tr>
          <td style="vertical-align:middle;">
            ${publications}
          </td>
          <td style="text-align:right; vertical-align:middle;">
            ${socials}
          </td>
        </tr>
      </table>
      ${bannerBlock}
    </td>
  </tr>
</table>
  `.trim();
}

async function updatePreview() {
  const values = normalizeValues(new FormData(form));
  const photoFile = form.elements.photo?.files?.[0];
  const bannerFile = form.elements.banner?.files?.[0];

  if (photoFile || bannerFile) {
    const uploadData = new FormData();
    if (photoFile) uploadData.append("photo", photoFile);
    if (bannerFile) uploadData.append("banner", bannerFile);

    try {
      const res = await fetch("api.php?action=upload", {
        method: "POST",
        body: uploadData,
      });
      const data = await res.json();
      if (data.photo) values.photoUrl = data.photo;
      if (data.banner) values.bannerUrl = data.banner;
      if (form.elements.photoUrl && data.photo) form.elements.photoUrl.value = data.photo;
      if (form.elements.bannerUrl && data.banner) form.elements.bannerUrl.value = data.banner;
    } catch (err) {
      if (photoFile) values.photoUrl = URL.createObjectURL(photoFile);
      if (bannerFile) values.bannerUrl = URL.createObjectURL(bannerFile);
    }
  }

  const html = buildSignature(values);
  previewArea.innerHTML = `<div class="signature">${html}</div>`;
  htmlOutput.textContent = html;
  document.documentElement.style.setProperty("--accent", values.accent || defaults.accent);
}

async function saveToDb() {
  const id = currentId();
  if (id) {
    await updateToDb(id);
    return;
  }
  const formData = new FormData(form);
  try {
    saveBtn.textContent = "Saving...";
    const res = await fetch("api.php?action=save", {
      method: "POST",
      body: formData,
    });
    const data = await res.json();
    if (data.photo && form.elements.photoUrl) {
      form.elements.photoUrl.value = data.photo;
    }
    if (data.banner && form.elements.bannerUrl) {
      form.elements.bannerUrl.value = data.banner;
    }
    if (data.id && form.elements.signatureId) {
      form.elements.signatureId.value = data.id;
    }
    await refreshSaved();
    updatePreviewWithUploads(data.photo, data.banner);
    saveBtn.textContent = "Saved!";
    setTimeout(() => (saveBtn.textContent = "Save to DB"), 1600);
  } catch (err) {
    saveBtn.textContent = "Save failed";
    setTimeout(() => (saveBtn.textContent = "Save to DB"), 2000);
  }
}

async function updateToDb(id) {
  const formData = new FormData(form);
  formData.append("id", id);
  try {
    saveBtn.textContent = "Updating...";
    const res = await fetch("api.php?action=update", {
      method: "POST",
      body: formData,
    });
    const data = await res.json();
    if (data.photo && form.elements.photoUrl) {
      form.elements.photoUrl.value = data.photo;
    }
    if (data.banner && form.elements.bannerUrl) {
      form.elements.bannerUrl.value = data.banner;
    }
    await refreshSaved();
    updatePreviewWithUploads(data.photo, data.banner);
    saveBtn.textContent = "Updated!";
    setTimeout(() => (saveBtn.textContent = "Save to DB"), 1600);
  } catch (err) {
    saveBtn.textContent = "Update failed";
    setTimeout(() => (saveBtn.textContent = "Save to DB"), 2000);
  }
}

function updatePreviewWithUploads(photoPath, bannerPath) {
  const values = normalizeValues(new FormData(form));
  values.photoUrl = photoPath || values.photoUrl;
  values.bannerUrl = bannerPath || values.bannerUrl;
  const html = buildSignature(values);
  previewArea.innerHTML = `<div class="signature">${html}</div>`;
  htmlOutput.textContent = html;
}

function copyHtml() {
  navigator.clipboard.writeText(htmlOutput.textContent).then(() => {
    copyBtn.textContent = "Copied!";
    setTimeout(() => (copyBtn.textContent = "Copy HTML"), 1600);
  });
}

async function deleteSignature() {
  const id = currentId();
  if (!id) return;
  if (!confirm("Delete this signature?")) return;
  try {
    deleteBtn.textContent = "Deleting...";
    await fetch("api.php?action=delete", {
      method: "POST",
      body: new URLSearchParams({ id }),
    });
    form.reset();
    if (form.elements.signatureId) form.elements.signatureId.value = "";
    if (form.elements.photoUrl) form.elements.photoUrl.value = "";
    if (form.elements.bannerUrl) form.elements.bannerUrl.value = "";
    setDefaults();
    updatePreview();
    await refreshSaved();
  } finally {
    deleteBtn.textContent = "Delete";
  }
}

async function exportPng() {
  if (!window.html2canvas) {
    alert("PNG export needs internet access to load html2canvas.");
    return;
  }
  const target = previewArea.querySelector(".signature");
  if (!target) return;
  exportBtn.textContent = "Exporting...";
  const canvas = await window.html2canvas(target, { backgroundColor: "#ffffff", scale: 2 });
  const link = document.createElement("a");
  link.download = "signature.png";
  link.href = canvas.toDataURL("image/png");
  link.click();
  exportBtn.textContent = "Export PNG";
}

async function refreshSaved() {
  if (!savedList) return;
  const res = await fetch("api.php?action=list");
  const data = await res.json();
  savedList.innerHTML = "";
  data.items.forEach((item) => {
    const row = document.createElement("button");
    row.type = "button";
    row.className = "saved-item";
    row.textContent = `${item.full_name} • ${item.email}`;
    row.addEventListener("click", () => loadSignature(item.id));
    savedList.appendChild(row);
  });
}

async function loadSignature(id) {
  const res = await fetch(`api.php?action=get&id=${id}`);
  const data = await res.json();
  const item = data.item;
  if (!item) return;
  if (form.elements.signatureId) {
    form.elements.signatureId.value = item.id || "";
  }
  if (form.elements.photoUrl) {
    form.elements.photoUrl.value = item.photo_path || "";
  }
  if (form.elements.bannerUrl) {
    form.elements.bannerUrl.value = item.banner_path || "";
  }
  Object.entries(item).forEach(([key, value]) => {
    const map = {
      full_name: "fullName",
      role_line: "roleLine",
      dept_line: "deptLine",
      photo_path: "photoUrl",
      banner_path: "bannerUrl",
    };
    const fieldName = map[key] || key;
    const field = form.elements[fieldName];
    if (field) field.value = value || "";
  });
  updatePreviewWithUploads(item.photo_path, item.banner_path);
}

setDefaults();
updatePreview();
if (savedList) {
  refreshSaved().catch(() => {});
}

if (generateBtn) generateBtn.addEventListener("click", updatePreview);
if (saveBtn) saveBtn.addEventListener("click", saveToDb);
if (deleteBtn) deleteBtn.addEventListener("click", deleteSignature);
if (copyBtn) copyBtn.addEventListener("click", copyHtml);
if (exportBtn) exportBtn.addEventListener("click", exportPng);
