// kalkulator.js - Rumus Interpolasi Z-Score Permenkes 2020 & Gizi Bumil

// ==========================================
// FUNGSI BANTUAN (Z-SCORE BALITA)
// ==========================================
function hitungZScore(nilai, sd7) {
    const [m3, m2, m1, med, p1, p2, p3] = sd7;
    let z = 0;

    if (nilai >= p3) {
        z = 3 + ((nilai - p3) / (p3 - p2));
    } else if (nilai >= p2) {
        z = 2 + ((nilai - p2) / (p3 - p2));
    } else if (nilai >= p1) {
        z = 1 + ((nilai - p1) / (p2 - p1));
    } else if (nilai >= med) {
        z = (nilai - med) / (p1 - med);
    } else if (nilai >= m1) {
        z = (nilai - med) / (med - m1);
    } else if (nilai >= m2) {
        z = -1 + ((nilai - m1) / (m1 - m2));
    } else if (nilai >= m3) {
        z = -2 + ((nilai - m2) / (m2 - m3));
    } else {
        z = -3 + ((nilai - m3) / (m2 - m3));
    }
    return parseFloat(z.toFixed(2));
}

// ==========================================
// KALKULATOR BALITA
// ==========================================
function hitungGiziBalita() {
    const gender  = document.getElementById("gender").value; 
    const umur    = parseInt(document.getElementById("umur").value);
    const bbRiil  = parseFloat(document.getElementById("bbRiil").value);
    const tbRiil  = parseFloat(document.getElementById("tbRiil").value);
    const resultDiv = document.getElementById("resultBalita");

    if (isNaN(umur) || isNaN(bbRiil) || isNaN(tbRiil)) {
        alert("Harap isi Umur, Berat Badan, dan Tinggi Badan Balita dengan benar!");
        return;
    }

    const dataBBU = datasetBBU[gender]?.[umur];
    const dataTBU = datasetTBU[gender]?.[umur];
    
    const bbtbSubKey = umur <= 24 ? "pb" : "tb";
    const datasetTB = datasetBBTB[gender]?.[bbtbSubKey];
    let dataBBTB = null;
    if (datasetTB) {
        const tbKey = Math.round(tbRiil);
        dataBBTB = datasetTB[tbKey];
    }

    if (!dataBBU || !dataTBU) {
        alert(`Data standar untuk umur ${umur} bulan tidak ditemukan.`);
        return;
    }

    const zBBU = hitungZScore(bbRiil, dataBBU);
    const zTBU = hitungZScore(tbRiil, dataTBU);
    const zBBTB = dataBBTB ? hitungZScore(bbRiil, dataBBTB) : null;

    let statusBBU = "", badgeBBU = "";
    if (zBBU < -3) { statusBBU = "Berat Badan Sangat Kurang"; badgeBBU = "danger"; }
    else if (zBBU < -2) { statusBBU = "Berat Badan Kurang"; badgeBBU = "warning"; }
    else if (zBBU <= 1) { statusBBU = "Berat Badan Normal"; badgeBBU = "success"; }
    else { statusBBU = "Risiko Berat Badan Lebih"; badgeBBU = "info"; }

    let statusTBU = "", badgeTBU = "";
    if (zTBU < -3) { statusTBU = "Sangat Pendek"; badgeTBU = "danger"; }
    else if (zTBU < -2) { statusTBU = "Pendek"; badgeTBU = "warning"; }
    else if (zTBU <= 3) { statusTBU = "Normal"; badgeTBU = "success"; }
    else { statusTBU = "Tinggi"; badgeTBU = "info"; }

    let statusBBTB = "Data tidak ditemukan", badgeBBTB = "info", zBBTBText = "-";
    if (zBBTB !== null) {
        zBBTBText = `${zBBTB} SD`;
        if (zBBTB < -3) { statusBBTB = "Gizi Buruk"; badgeBBTB = "danger"; }
        else if (zBBTB < -2) { statusBBTB = "Gizi Kurang"; badgeBBTB = "warning"; }
        else if (zBBTB <= 1) { statusBBTB = "Gizi Baik"; badgeBBTB = "success"; }
        else if (zBBTB <= 2) { statusBBTB = "Berisiko Gizi Lebih"; badgeBBTB = "info"; }
        else if (zBBTB <= 3) { statusBBTB = "Gizi Lebih"; badgeBBTB = "warning"; }
        else { statusBBTB = "Obesitas"; badgeBBTB = "danger"; }
    }

    // Mapping warna Tailwind
    const colors = {
        danger: 'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-amber-50 border-amber-200 text-amber-800',
        success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800'
    };

    resultDiv.style.display = "flex";
    resultDiv.innerHTML = `
        <div class="p-4 rounded-xl border ${colors[badgeBBU]} mb-2">
            <h4 class="font-bold text-sm mb-1">1. Berat Badan Menurut Umur (BB/U)</h4>
            <p class="text-sm"><strong>Z-Score:</strong> ${zBBU} SD | <strong>Status:</strong> ${statusBBU}</p>
        </div>
        <div class="p-4 rounded-xl border ${colors[badgeTBU]} mb-2">
            <h4 class="font-bold text-sm mb-1">2. Tinggi/Panjang Badan Menurut Umur (TB/U)</h4>
            <p class="text-sm"><strong>Z-Score:</strong> ${zTBU} SD | <strong>Status:</strong> ${statusTBU}</p>
        </div>
        <div class="p-4 rounded-xl border ${colors[badgeBBTB]}">
            <h4 class="font-bold text-sm mb-1">3. Berat Badan Menurut Tinggi Badan (BB/TB)</h4>
            <p class="text-sm"><strong>Z-Score:</strong> ${zBBTBText} | <strong>Status:</strong> ${statusBBTB}</p>
        </div>
    `;
}

// ==========================================
// KALKULATOR IBU HAMIL (BUMIL)
// ==========================================
function hitungGiziBumil() {
    const tb         = parseFloat(document.getElementById("tbBumil").value);
    const bbPra      = parseFloat(document.getElementById("bbPra").value);
    const bbSkrg     = parseFloat(document.getElementById("bbSkrg").value);
    const usiaHamil  = parseInt(document.getElementById("usiaHamil").value);
    const lilaInput  = document.getElementById("lila").value;
    const hbInput    = document.getElementById("hb").value;
    const resultDiv  = document.getElementById("resultBumil");

    if (isNaN(tb) || isNaN(bbPra) || isNaN(bbSkrg) || isNaN(usiaHamil)) {
        alert("Harap isi variabel wajib (TB, BB Sebelum Hamil, BB Saat Ini, Usia Kehamilan)!");
        return;
    }

    // Hitung IMT Pra-Hamil
    const tbMeter = tb / 100;
    const imtPraFixed = parseFloat((bbPra / (tbMeter * tbMeter)).toFixed(1));
    const deltaBBFixed = parseFloat((bbSkrg - bbPra).toFixed(1));

    let kategoriIMT = "", badgeIMT = "", lajuBBMingguan = 0, minTotalGWG = 0, maxTotalGWG = 0;

    if (imtPraFixed < 18.5) {
        kategoriIMT = "Kurus (< 18.5)"; badgeIMT = "warning";
        minTotalGWG = 12.5; maxTotalGWG = 18.0; lajuBBMingguan = 0.51;
    } else if (imtPraFixed <= 24.9) {
        kategoriIMT = "Normal (18.5 - 24.9)"; badgeIMT = "success";
        minTotalGWG = 11.5; maxTotalGWG = 16.0; lajuBBMingguan = 0.42;
    } else if (imtPraFixed <= 29.9) {
        kategoriIMT = "Gemuk (25.0 - 29.9)"; badgeIMT = "warning";
        minTotalGWG = 7.0; maxTotalGWG = 11.5; lajuBBMingguan = 0.28;
    } else {
        kategoriIMT = "Obesitas (≥ 30.0)"; badgeIMT = "danger";
        minTotalGWG = 5.0; maxTotalGWG = 9.0; lajuBBMingguan = 0.22;
    }

    // Target Kenaikan BB berdasarkan Trimester
    let targetMin = 0, targetMax = 0, statusGWG = "", badgeGWG = "";
    if (usiaHamil <= 12) {
        targetMin = 0.5;
        targetMax = 2.0;
    } else {
        const mingguTri23 = usiaHamil - 12;
        targetMin = 1.0 + (lajuBBMingguan * 0.8 * mingguTri23);
        targetMax = 2.0 + (lajuBBMingguan * 1.2 * mingguTri23);
    }
    targetMin = parseFloat(targetMin.toFixed(1));
    targetMax = parseFloat(targetMax.toFixed(1));

    if (deltaBBFixed < targetMin)         { statusGWG = "Kurang dari target"; badgeGWG = "warning"; }
    else if (deltaBBFixed <= targetMax)   { statusGWG = "Sesuai target (Ideal)"; badgeGWG = "success"; }
    else                                  { statusGWG = "Melebihi target"; badgeGWG = "danger"; }

    // Cek LILA & HB (Opsional)
    let htmlLiLA = "", htmlHb = "";
    if (lilaInput !== "") {
        const lila = parseFloat(lilaInput);
        const isKEK = lila < 23.5;
        htmlLiLA = `
            <div class="p-4 rounded-xl border ${isKEK ? 'bg-red-50 border-red-200 text-red-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800'} mb-2">
                <h4 class="font-bold text-sm mb-1">3. Risiko Kurang Energi Kronis (LiLA)</h4>
                <p class="text-sm"><strong>Hasil:</strong> ${lila} cm | <strong>Status:</strong> ${isKEK ? 'Berisiko KEK' : 'Normal / Aman'}</p>
            </div>`;
    }

    if (hbInput !== "") {
        const hb = parseFloat(hbInput);
        const isAnemia = hb < 11.0;
        htmlHb = `
            <div class="p-4 rounded-xl border ${isAnemia ? 'bg-red-50 border-red-200 text-red-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800'}">
                <h4 class="font-bold text-sm mb-1">4. Kadar Hemoglobin (Hb)</h4>
                <p class="text-sm"><strong>Hasil:</strong> ${hb} g/dL | <strong>Status:</strong> ${isAnemia ? 'Anemia' : 'Normal'}</p>
            </div>`;
    }

    // Mapping warna Tailwind untuk Bumil
    const colors = {
        danger: 'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-amber-50 border-amber-200 text-amber-800',
        success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800'
    };

    resultDiv.style.display = "flex";
    resultDiv.innerHTML = `
        <div class="p-4 rounded-xl border ${colors[badgeIMT]} mb-2">
            <h4 class="font-bold text-sm mb-1">1. Status Gizi Awal (IMT Pra-Hamil)</h4>
            <p class="text-sm"><strong>IMT:</strong> ${imtPraFixed} kg/m² | <strong>Kategori:</strong> ${kategoriIMT}</p>
            <p class="text-xs mt-1 opacity-80">Rekomendasi kenaikan BB total: ${minTotalGWG} - ${maxTotalGWG} kg</p>
        </div>
        <div class="p-4 rounded-xl border ${colors[badgeGWG]} mb-2">
            <h4 class="font-bold text-sm mb-1">2. Evaluasi Kenaikan Berat Badan</h4>
            <p class="text-sm"><strong>Kenaikan BB (ΔBB):</strong> ${deltaBBFixed} kg</p>
            <p class="text-sm"><strong>Target Usia ${usiaHamil} Mgg:</strong> ${targetMin} s/d ${targetMax} kg</p>
            <p class="text-sm font-bold mt-1">Evaluasi: ${statusGWG}</p>
        </div>
        ${htmlLiLA}
        ${htmlHb}
    `;
}