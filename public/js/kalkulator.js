// ============================================
// KALKULATOR GIZI PETANGBULA
// ============================================

function switchTab(tabName) {
  const tabBalita = document.getElementById('tab-balita');
  const tabBumil  = document.getElementById('tab-bumil');
  const btnBalita = document.getElementById('btn-tab-balita');
  const btnBumil  = document.getElementById('btn-tab-bumil');

  if (tabName === 'balita') {
      tabBalita.classList.add('active');
      tabBumil.classList.remove('active');
      btnBalita.classList.add('active');
      btnBumil.classList.remove('active');
  } else {
      tabBumil.classList.add('active');
      tabBalita.classList.remove('active');
      btnBumil.classList.add('active');
      btnBalita.classList.remove('active');
  }
}

function calculateZScore(nilaiRiil, dataStandar) {
  const { median, sd1pos, sd1neg } = dataStandar;
  let zScoreNum = 0;

  if (nilaiRiil >= median) {
      zScoreNum = (nilaiRiil - median) / (sd1pos - median);
  } else {
      zScoreNum = (nilaiRiil - median) / (median - sd1neg);
  }

  return parseFloat(zScoreNum.toFixed(2));
}

// Cari kunci terdekat dalam objek dataset
function getClosestKeyInObject(obj, targetValue) {
  let closestKey = null;
  let minDiff = Infinity;
  Object.keys(obj).forEach(key => {
      const diff = Math.abs(parseFloat(key) - targetValue);
      if (diff < minDiff) {
          minDiff = diff;
          closestKey = key;
      }
  });
  return closestKey;
}

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

  const dataBBU = datasetBBU?.[gender]?.[umur];
  const dataTBU = datasetTBU?.[gender]?.[umur];

  if (!dataBBU || !dataTBU) {
      alert(`Data standar BB/U atau TB/U untuk umur ${umur} bulan tidak ditemukan.`);
      return;
  }

  // ======== CARI DATA BB/TB TERDEKAT ========
  const bbtbSubKey = umur <= 24 ? "pb_0_24" : "tb_24_60";
  const labelIndikatorBBTB = umur <= 24
      ? "3. Berat Badan Menurut Panjang Badan (BB/PB)"
      : "3. Berat Badan Menurut Tinggi Badan (BB/TB)";

  const datasetTB = datasetBBPB_BBTB?.[gender]?.[bbtbSubKey];
  let dataBBTB = null;

  if (datasetTB) {
      const matchedKey = getClosestKeyInObject(datasetTB, tbRiil);
      if (matchedKey) {
          dataBBTB = datasetTB[matchedKey];
      }
  }

  // A. Indikator BB/U
  const zBBU = calculateZScore(bbRiil, dataBBU);
  let statusBBU = "", badgeBBU = "";
  if (zBBU < -3.0)       { statusBBU = "Berat Badan Sangat Kurang"; badgeBBU = "danger"; }
  else if (zBBU < -2.0)  { statusBBU = "Berat Badan Kurang"; badgeBBU = "warning"; }
  else if (zBBU <= 1.0)  { statusBBU = "Berat Badan Normal"; badgeBBU = "success"; }
  else                   { statusBBU = "Risiko Berat Badan Lebih"; badgeBBU = "info"; }

  // B. Indikator TB/U
  const zTBU = calculateZScore(tbRiil, dataTBU);
  let statusTBU = "", badgeTBU = "";
  if (zTBU < -3.0)       { statusTBU = "Sangat Pendek (Severely Stunted)"; badgeTBU = "danger"; }
  else if (zTBU < -2.0)  { statusTBU = "Pendek (Stunted)"; badgeTBU = "warning"; }
  else if (zTBU <= 2.0)  { statusTBU = "Normal"; badgeTBU = "success"; }
  else                   { statusTBU = "Tinggi"; badgeTBU = "info"; }

  // C. Indikator BB/TB
  let statusBBTB = "Data tidak ditemukan", badgeBBTB = "info", zBBTBText = "-";
  if (dataBBTB) {
      const zBBTB = calculateZScore(bbRiil, dataBBTB);
      zBBTBText = `${zBBTB} SD`;
      if (zBBTB < -3.0)      { statusBBTB = "Gizi Buruk (Severely Wasted)"; badgeBBTB = "danger"; }
      else if (zBBTB < -2.0) { statusBBTB = "Gizi Kurang (Wasted)"; badgeBBTB = "warning"; }
      else if (zBBTB <= 1.0) { statusBBTB = "Gizi Baik (Normal)"; badgeBBTB = "success"; }
      else if (zBBTB <= 2.0) { statusBBTB = "Berisiko Gizi Lebih"; badgeBBTB = "info"; }
      else if (zBBTB <= 3.0) { statusBBTB = "Gizi Lebih (Overweight)"; badgeBBTB = "warning"; }
      else                   { statusBBTB = "Obesitas"; badgeBBTB = "danger"; }
  }

  resultDiv.style.display = "flex";
  resultDiv.innerHTML = `
      <div class="result-card ${badgeBBU}">
          <h4>1. Berat Badan Menurut Umur (BB/U)</h4>
          <p><strong>Z-Score:</strong> ${zBBU} SD | <strong>Status:</strong> ${statusBBU}</p>
      </div>
      <div class="result-card ${badgeTBU}">
          <h4>2. Tinggi/Panjang Badan Menurut Umur (${umur <= 24 ? "PB/U" : "TB/U"})</h4>
          <p><strong>Z-Score:</strong> ${zTBU} SD | <strong>Status:</strong> ${statusTBU}</p>
      </div>
      <div class="result-card ${badgeBBTB}">
          <h4>${labelIndikatorBBTB}</h4>
          <p><strong>Z-Score:</strong> ${zBBTBText} | <strong>Status:</strong> ${statusBBTB}</p>
      </div>
  `;
}

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

  let htmlLiLA = "", htmlHb = "";
  if (lilaInput !== "") {
      const lila = parseFloat(lilaInput);
      const isKEK = lila < 23.5;
      htmlLiLA = `
          <div class="result-card ${isKEK ? 'danger' : 'success'}">
              <h4>3. Risiko Kurang Energi Kronis (LiLA)</h4>
              <p><strong>Hasil:</strong> ${lila} cm | <strong>Status:</strong> ${isKEK ? 'Berisiko KEK' : 'Normal / Aman'}</p>
          </div>`;
  }

  if (hbInput !== "") {
      const hb = parseFloat(hbInput);
      const isAnemia = hb < 11.0;
      htmlHb = `
          <div class="result-card ${isAnemia ? 'danger' : 'success'}">
              <h4>4. Kadar Hemoglobin (Hb)</h4>
              <p><strong>Hasil:</strong> ${hb} g/dL | <strong>Status:</strong> ${isAnemia ? 'Anemia' : 'Normal'}</p>
          </div>`;
  }

  resultDiv.style.display = "flex";
  resultDiv.innerHTML = `
      <div class="result-card ${badgeIMT}">
          <h4>1. Status Gizi Awal (IMT Pra-Hamil)</h4>
          <p><strong>IMT:</strong> ${imtPraFixed} kg/m² | <strong>Kategori:</strong> ${kategoriIMT}</p>
          <p style="font-size:0.78rem; margin-top:2px; opacity:0.8;">Rekomendasi kenaikan BB total: ${minTotalGWG} - ${maxTotalGWG} kg</p>
      </div>
      <div class="result-card ${badgeGWG}">
          <h4>2. Evaluasi Kenaikan Berat Badan Saat Ini</h4>
          <p><strong>Kenaikan BB (ΔBB):</strong> ${deltaBBFixed} kg</p>
          <p><strong>Target Usia Hamil ${usiaHamil} Mgg:</strong> ${targetMin} s/d ${targetMax} kg</p>
          <p><strong>Evaluasi:</strong> ${statusGWG}</p>
      </div>
      ${htmlLiLA}
      ${htmlHb}
  `;
}