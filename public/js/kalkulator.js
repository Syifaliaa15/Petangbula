// kalkulator.js - Rumus Interpolasi Z-Score Permenkes 2020

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

function hitungGiziBalita() {
    const gender  = document.getElementById("gender").value; // 'L' atau 'P'
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
    
    // Ambil data BB/TB (gunakan logika pencarian terdekat jika ada, atau asumsikan data di-key berdasarkan cm)
    const bbtbSubKey = umur <= 24 ? "pb" : "tb";
    const datasetTB = datasetBBTB[gender]?.[bbtbSubKey];
    let dataBBTB = null;
    if (datasetTB) {
        // Cari kunci cm terdekat
        const tbKey = Math.round(tbRiil);
        dataBBTB = datasetTB[tbKey];
    }

    if (!dataBBU || !dataTBU) {
        alert(`Data standar untuk umur ${umur} bulan tidak ditemukan.`);
        return;
    }

    // Hitung Z-Score
    const zBBU = hitungZScore(bbRiil, dataBBU);
    const zTBU = hitungZScore(tbRiil, dataTBU);
    const zBBTB = dataBBTB ? hitungZScore(bbRiil, dataBBTB) : null;

    // Tentukan Kategori (Sesuai Label Permenkes 2020)
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

    // Tampilkan Hasil
    resultDiv.style.display = "flex";
    resultDiv.innerHTML = `
        <div class="result-card ${badgeBBU}">
            <h4>1. Berat Badan Menurut Umur (BB/U)</h4>
            <p><strong>Z-Score:</strong> ${zBBU} SD | <strong>Status:</strong> ${statusBBU}</p>
        </div>
        <div class="result-card ${badgeTBU}">
            <h4>2. Tinggi Badan Menurut Umur (TB/U)</h4>
            <p><strong>Z-Score:</strong> ${zTBU} SD | <strong>Status:</strong> ${statusTBU}</p>
        </div>
        <div class="result-card ${badgeBBTB}">
            <h4>3. Berat Badan Menurut Tinggi Badan (BB/TB)</h4>
            <p><strong>Z-Score:</strong> ${zBBTBText} | <strong>Status:</strong> ${statusBBTB}</p>
        </div>
    `;
}