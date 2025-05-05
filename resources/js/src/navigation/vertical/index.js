import beranda from './1_beranda'
import referensi from './2_referensi'
//import ujian from './9_ujian'
import laporan from './3_laporan'
import pelanggaran from './3_pelanggaran'
//import kehadiran from './4_kehadiran'
//import ketidakhadiran from './5_ketidakhadiran'
import rekapitulasi from './6_rekapitulasi'
import pengaturan from './7_pengaturan'
//import admin from './8_admin'
import bottom from './99_bottom'

export default [
    ...beranda, 
    ...referensi,
    //...ujian,
    ...pelanggaran,
    ...laporan,
    //...kehadiran,
    //...ketidakhadiran,
    ...rekapitulasi,
    ...pengaturan,
    //...admin,
    ...bottom
]