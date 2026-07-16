const namaBulan = [
  "",
  "Januari",
  "Februari",
  "Maret",
  "April",
  "Mei",
  "Juni",
  "Juli",
  "Agustus",
  "September",
  "Oktober",
  "November",
  "Desember",
];

export function formatPeriode(bulan, tahun) {
  const nomorBulan = Number(bulan);
  const labelBulan = namaBulan[nomorBulan] || bulan;

  return [labelBulan, tahun].filter(Boolean).join(" ") || "-";
}
