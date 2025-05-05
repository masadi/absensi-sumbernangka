<?php
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Sekolah;
use App\Models\Peserta_didik;
use App\Models\Ptk;
use App\Models\Jam_ptk;
use App\Models\Jam_pd;
use App\Models\Jam_hari;
use App\Models\Absen;
use App\Models\Semester;
use App\Models\Libur;
use App\Models\Izin;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\File;

function jam($menit){
    return $menit / 60;//intdiv($menit, 60).':'. ($menit % 60);
}
function jam_angka($menit){
    return intdiv($menit, 60).'.'. ($menit % 60);
}
function jam_aktif_pd($peserta_didik_id){
    $jam_pd = Jam_pd::with(['jam'])->where('peserta_didik_id', $peserta_didik_id)->first();
    $from = Carbon::now()->format('H:i:s');
    $to = Carbon::createFromFormat('H:i:s', $jam_pd->jam->jam_pulang);
    return $to->diffInHours($from);
}
function hari($menit){
    $hid = 24; // Hours in a day - could be 24, 8, etc
    $days = ($menit) ? jam_angka($menit)/$hid : 0;//round(jam_angka($menit)/$hid);
    return $days;
}
function tidak_hadir_pd($peserta_didik_id, $start, $end){
    $period = CarbonPeriod::between($start, $end)->addFilter(function ($date) {
        return !$date->isFriday();
        //$date->isMonday() || $date->isTuesday() || $date->isWednesday() || $date->isThursday() || $date->isFriday();
    });
    $libur = Libur::select('mulai_tanggal')->where(function($query) use ($period, $start, $end){
        $query->whereDate('mulai_tanggal', '>=', $start);
        $query->whereDate('sampai_tanggal', '<=', $end);
        $query->where(function($q) use($period) {
            collect($period->map(function (Carbon $date) use ($q){
                $q->whereDay('mulai_tanggal', '=', $date->format('d'), 'or');
                $q->whereDay('sampai_tanggal', '=', $date->format('d'), 'or');
            }));
        });
    })->get();
    $hari_libur = NULL;
    foreach ($libur as $value) {
        $hari_libur[] = date('Y-m-d', strtotime($value->mulai_tanggal));
    }
    $tidak_hadir = Absen::where(function($query) use ($peserta_didik_id, $start, $end, $hari_libur){
        $query->doesntHave('absen_masuk');
        $query->whereDoesntHave('izin', function($query){
            $query->where('jenis', '<>', 'Sekolah');
        });
        $query->where('peserta_didik_id', $peserta_didik_id);
        $query->whereDate('created_at', '>=', $start);
        $query->whereDate('created_at', '<=', $end);
        if($hari_libur){
            $query->whereNotIn('created_at', $hari_libur);
        }
    })->count();
    return $tidak_hadir;//jml_hari_aktif($start, $end) - $jml_hadir;
}
function total_hadir_ptk($ptk_id, $start, $end){
    return Absen::where(function($query) use ($ptk_id, $start, $end){
        $query->whereHas('absen_masuk');
        $query->where('ptk_id', $ptk_id);
        $query->whereDate('created_at', '>=', $start);
        $query->whereDate('created_at', '<=', $end);
        $query->orwhereHas('izin', function($query){
            $query->where('jenis', 'Sekolah');
        });
        $query->where('ptk_id', $ptk_id);
        $query->whereDate('created_at', '>=', $start);
        $query->whereDate('created_at', '<=', $end);
    })->count();
}
function total_hadir_pd($peserta_didik_id, $start, $end){
    return Absen::where(function($query) use ($peserta_didik_id, $start, $end){
        $query->whereHas('absen_masuk');
        $query->where('peserta_didik_id', $peserta_didik_id);
        $query->whereDate('created_at', '>=', $start);
        $query->whereDate('created_at', '<=', $end);
        $query->orwhereHas('izin', function($query){
            $query->where('jenis', 'Sekolah');
        });
        $query->where('peserta_didik_id', $peserta_didik_id);
        $query->whereDate('created_at', '>=', $start);
        $query->whereDate('created_at', '<=', $end);
    })->count();
}
function tidak_hadir_ptk($ptk_id, $start, $end, $total = TRUE){
    $period = CarbonPeriod::between($start, $end)->addFilter(function ($date) {
        return !$date->isFriday();
        //$date->isMonday() || $date->isTuesday() || $date->isWednesday() || $date->isThursday() || $date->isFriday();
    });
    $libur = Libur::where(function($query) use ($period, $start, $end){
        $query->whereDate('mulai_tanggal', '>=', $start);
        $query->whereDate('sampai_tanggal', '<=', $end);
        $query->where(function($q) use($period) {
            collect($period->map(function (Carbon $date) use ($q){
                $q->whereDay('mulai_tanggal', '=', $date->format('d'), 'or');
                $q->whereDay('sampai_tanggal', '=', $date->format('d'), 'or');
            }));
        });
    })->get();
    $hari_libur = NULL;
    foreach ($libur as $value) {
        $hari_libur[] = date('Y-m-d', strtotime($value->mulai_tanggal));
    }
    $tidak_hadir = Absen::where(function($query) use ($ptk_id, $start, $end, $hari_libur, $total){
        $query->doesntHave('absen_masuk');
        $query->whereDoesntHave('izin', function($query) use ($total){
            if($total){
                $query->where('jenis', 'Sekolah');
            }
        });
        $query->where('ptk_id', $ptk_id);
        $query->whereDate('created_at', '>=', $start);
        $query->whereDate('created_at', '<=', $end);
        if($hari_libur){
            $query->whereNotIn('created_at', $hari_libur);
        }
    })->count();
    return $tidak_hadir;
}
function jml_hari_aktif($start = NULL, $end = NULL){
    $semester = Semester::find(semester_id());
    if($end){
        $from = Carbon::createFromFormat('Y-m-d', $start);
        $to = Carbon::createFromFormat('Y-m-d', $end);
    } else {
        $from = Carbon::createFromFormat('Y-m-d', $semester->tanggal_mulai);
        $to = Carbon::createFromFormat('Y-m-d', $semester->tanggal_selesai);
    }
    $libur = Libur::where(function($query) use ($from, $to){
        $query->whereDate('created_at', '>=', $from);
        $query->whereDate('created_at', '<=', $to);
    })->count();
    $jml_hari_aktif = $to->diffInDays($from) - $libur;
    return $jml_hari_aktif;
}
function tglIndo($tanggal){
    $date = Carbon::createFromFormat('Y-m-d', $tanggal);
    return $date->translatedFormat('j F Y');
}
function jml_hari_aktif_ptk($sekolah_id, $ptk_id, $start = NULL, $end = NULL){
    if($end){
        $from = Carbon::createFromFormat('Y-m-d', $start);
        $to = Carbon::createFromFormat('Y-m-d', $end);
    } else {
        $semester = Semester::find(semester_id());
        $from = Carbon::createFromFormat('Y-m-d', $semester->tanggal_mulai);
        $to = Carbon::createFromFormat('Y-m-d', $semester->tanggal_selesai);
    }
    $jam_ptk = Jam_ptk::whereHas('jam', function($query){
        $query->where('semester_id', semester_id());
    })->where('ptk_id', $ptk_id)->get();
    $nama_hari = [];
    if($jam_ptk){
        foreach($jam_ptk as $jjm){
            foreach($jjm->jam->hari as $hari){
                $nama_hari[$hari->nama] = $hari->nama;
            }
        }
        $period = CarbonPeriod::between($start, $end)
        ->addFilter(function ($date) use ($nama_hari){
            return in_array($date->translatedFormat('l'), $nama_hari);
        });
        $libur = jml_hari_libur($sekolah_id, $start, $end, $period, $nama_hari);
        $jml_hari_aktif = $period->count() - $libur;
    } else {
        $jml_hari_aktif = 0;
        $libur = 0;
    }
    return [
        'jml_hari_aktif' => $jml_hari_aktif,
        'libur' => $libur,
    ];
}
function jml_hari_aktif_pd($sekolah_id, $peserta_didik_id, $start = NULL, $end = NULL){
    if($end){
        $from = Carbon::createFromFormat('Y-m-d', $start);
        $to = Carbon::createFromFormat('Y-m-d', $end);
    } else {
        $semester = Semester::find(semester_id());
        $from = Carbon::createFromFormat('Y-m-d', $semester->tanggal_mulai);
        $to = Carbon::createFromFormat('Y-m-d', $semester->tanggal_selesai);
    }
    $jam_pd = Jam_pd::whereHas('jam', function($query){
        $query->where('semester_id', semester_id());
    })->where('peserta_didik_id', $peserta_didik_id)->first();
    if($jam_pd){
        $nama_hari = [];
        foreach($jam_pd->jam->hari as $hari){
            $nama_hari[] = $hari->nama;
        }
        $period = CarbonPeriod::between($start, $end)
        ->addFilter(function ($date) use ($nama_hari){
            return in_array($date->translatedFormat('l'), $nama_hari);
        });
        $libur = jml_hari_libur($sekolah_id, $start, $end, $period, $nama_hari);
        $jml_hari_aktif = $period->count() - $libur;
    } else {
        $libur = 0;
        $jml_hari_aktif = 0;
    }
    return [
        'jml_hari_aktif' => $jml_hari_aktif,
        'libur' => $libur,
    ];
}
function jml_hari_libur($sekolah_id, $from, $to, $period, $nama_hari){
    $libur = Libur::where(function($query) use ($from, $to, $period, $sekolah_id){
        $query->whereHas('kategori_libur', function($query) use ($sekolah_id){
            $query->where('sekolah_id', $sekolah_id);
            $query->orWhereNull('sekolah_id');
        });
        $query->whereDate('mulai_tanggal', '>=', $from);
        $query->whereDate('sampai_tanggal', '<=', $to);
        $query->where(function($q) use($period) {
            collect($period->map(function (Carbon $date) use ($q){
                $q->whereDay('mulai_tanggal', '=', $date->format('d'), 'or');
                $q->whereDay('sampai_tanggal', '=', $date->format('d'), 'or');
            }));
        });
    })->get();
    $jml = 0;
    foreach($libur as $li){
        $jml += CarbonPeriod::between($li->mulai_tanggal, $li->mulai_tanggal)
        ->addFilter(function ($date) use ($nama_hari){
            return in_array($date->translatedFormat('l'), $nama_hari);
        })->count();
    }
    return $jml;
}
function izin_ptk($data, $jenis_izin, $start, $end){
    return $data->izin()->where(function($query) use ($jenis_izin, $start, $end){
        $query->where('izin.keterangan', $jenis_izin);
        $query->whereHas('absen', function($query) use ($start, $end){
            $query->whereDate('created_at', '>=', $start);
            $query->whereDate('created_at', '<=', $end);
        });
    })->count();
}
function izin_pd($data, $jenis_izin, $start, $end){
    return $data->izin()->where(function($query) use ($jenis_izin, $start, $end){
        $query->where('izin.keterangan', $jenis_izin);
        $query->whereHas('absen', function($query) use ($start, $end){
            $query->whereDate('created_at', '>=', $start);
            $query->whereDate('created_at', '<=', $end);
        });
    })->count();
}
function generate_qrcode($id){
    $folder = storage_path('app/public/qrcodes');
    if (!File::isDirectory($folder)) {
        //MAKA FOLDER TERSEBUT AKAN DIBUAT
        File::makeDirectory($folder, 0777, true, true);
    }
    if(!File::exists(storage_path('app/public/qrcodes/'.$id.'.svg'))){
        QrCode::size(200)->generate($id, storage_path('app/public/qrcodes/'.$id.'.svg'));
    }
}
function detik_ini(){
    return strtotime(Carbon::now()->format('H:i:s'));
}
function check_scan_masuk_start($jam){
    if(detik_ini() >= strtotime($jam)){
        return true;
    }
    return false;
}
function check_scan_masuk_end($jam){
    if(detik_ini() > strtotime($jam)){
        return false;
    }
    return true;
}
function check_scan_pulang_start($jam){
    if(detik_ini() >= strtotime($jam)){
        return true;
    }
    return false;
}
function check_scan_pulang_end($jam){
    if(detik_ini() > strtotime($jam)){
        return false;
    }
    return true;
}
function insert_absen($peserta_didik_id, $semester_id){
    $absen = Absen::where(function($query) use ($peserta_didik_id, $semester_id){
        $query->whereDate('created_at', Carbon::today());
        $query->where('peserta_didik_id', $peserta_didik_id);
        $query->where('semester_id', $semester_id);
    })->first();
    if($absen){
        $absen->updated_at = now();
        $absen->save();
    } else {
        $absen = Absen::create([
            'peserta_didik_id' => $peserta_didik_id,
            'semester_id' => $semester_id,
        ]);
    }
    return $absen;
}
function insert_absen_ptk($ptk_id, $semester_id){
    $absen = Absen::where(function($query) use ($ptk_id, $semester_id){
        $query->whereDate('created_at', Carbon::today());
        $query->where('ptk_id', $ptk_id);
        $query->where('semester_id', $semester_id);
    })->first();
    if($absen){
        $absen->updated_at = now();
        $absen->save();
    } else {
        $absen = Absen::create([
            'ptk_id' => $ptk_id,
            'semester_id' => $semester_id,
        ]);
    }
    return $absen;
}
function cek_libur($tanggal){
    return Libur::where(function($query) use ($tanggal){
        $query->whereDate('mulai_tanggal', '>=', $tanggal);
        $query->whereDate('sampai_tanggal', '<=', $tanggal);
    })->first();
}
function clean($string){
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}
function semester_id(){
    $data = Semester::where('periode_aktif', 1)->first();
    return ($data) ? $data->semester_id : NULL;
}
function nama_smt($semester_id){
    return str_replace(date('Y').'/'.(date('Y')+1), '', $semester_id);
}
function nama_tapel($nama){
    return str_replace('Ganjil', '', str_replace('Genap', '', $nama));
}
function data_sekolah(){
    return Sekolah::select('sekolah_id', 'nama')->get();
}
function bentuk_pendidikan_id($id){
    $bentuk = [
        1 => 'TK',
        5 => 'SD',
        6 => 'SMP',
        13 => 'SMA',
        15 => 'SMK',
    ];
    return isset($bentuk[$id]) ? $bentuk[$id] : '-';
}
function sd_square($x, $mean) { return pow($x - $mean,2); }
function sd($array) {
    // square root of sum of squares devided by N-1
    return sqrt(array_sum(array_map("sd_square", $array, array_fill(0,count($array), (array_sum($array) / count($array)) ) ) ) / (count($array)-1) );
}
function dataInduk($soal_ujian, $soal, $index, $items){
    $jumlah_soal = $soal_ujian->jumlah_soal;
    $skor_benar = $soal_ujian->skor_benar ?? 2;
    $skor_salah = $soal_ujian->skor_salah ?? 0;
    $skala_nilai = $soal_ujian->skala_nilai ?? 100;
    $total_skor = $jumlah_soal * $skor_benar;
    $rerata_nilai = [];
    $rerata_skor = [];
    $jumlah_peserta = 0;
    foreach($items as $item){
        $jumlah_peserta++;
        $benar = 0;
        $salah = 0;
        if(isset($item->anggota_rombel->jawaban_pd[$index])){
            if ($item->anggota_rombel->jawaban_pd[$index]->jawaban == $soal->jawaban){
                $benar++;
            } else {
                $salah++;
            }
        }
        $skor = $benar * $skor_benar - $salah * $skor_salah;
        $nilai = ($skor) ? ($skor / $skala_nilai) * ($jumlah_soal * $skor_benar): 0;
        $rerata_nilai[] = $nilai;
        $rerata_skor[] = $skor;
    }
    $BH57 = (array_sum($rerata_nilai)) ? array_sum($rerata_nilai)/count(array_filter($rerata_nilai)) : 0;
    $BE60 = (array_sum($rerata_skor)) ? array_sum($rerata_skor)/count(array_filter($rerata_skor)) : 0;
    $BE61 = (array_sum($rerata_nilai)) ? sd($rerata_nilai) : 0;
    $BH58 = (array_sum($rerata_nilai)) ? array_sum($rerata_nilai)/$jumlah_peserta : 0;
    $BH63 = 0;
    if(array_sum($rerata_nilai)){
        $BH63 = (1/sqrt(2*pi()))*exp(-0.5*$BH58);
    }
    $BH64 = 0;
    if(array_sum($rerata_nilai)){
        $BH64 = (($BH57-$BE60)/$BE61)*($BH58/$BH63);
    }
    return [
        'BH64' => $BH64,
        'jml_jawaban_benar' => $rerata_nilai,
    ];
}
function getBiser($soal_ujian, $soal, $index, $items){
    $dataInduk = dataInduk($soal_ujian, $soal, $index, $items);
    return number_format($dataInduk['BH64'],3,",",".");
}
function jawaban_benar($soal_ujian, $items, $index, $soal){
    $jumlah_soal = $soal_ujian->jumlah_soal;
    $skor_benar = $soal_ujian->skor_benar ?? 2;
    $skor_salah = $soal_ujian->skor_salah ?? 0;
    $skala_nilai = $soal_ujian->skala_nilai ?? 100;
    $total_skor = $jumlah_soal * $skor_benar;
    $rerata_nilai = [];
    foreach($items as $item){
        $benar = 0;
        $salah = 0;
        if(isset($item->anggota_rombel->jawaban_pd[$index])){
            if ($item->anggota_rombel->jawaban_pd[$index]->jawaban == $soal->jawaban){
                $benar++;
            } else {
                $salah++;
            }
        }
        $skor = $benar * $skor_benar - $salah * $skor_salah;
        $nilai = ($skor) ? ($skor / $skala_nilai) * ($jumlah_soal * $skor_benar): 0;
        $rerata_nilai[] = $nilai;
    }
    return $rerata_nilai;
}
function getPhpCorrelation($soal_ujian, $soal, $index, $items){
    $dataInduk = dataInduk($soal_ujian, $soal, $index, $items);
    $jawaban_benar = jawaban_benar($soal_ujian, $items, $index, $soal);
    $jml_jawaban_benar = $dataInduk['jml_jawaban_benar'];
    return php_correlation($jawaban_benar, $jml_jawaban_benar);
}
function php_correlation($x,$y){
    //PEARSON(Proses!E7:E56;Proses!$BC$7:$BC$56)
    if(count($x)!==count($y)){return -1;}   
    $x=array_values($x);
    $y=array_values($y);    
    $xs=array_sum($x)/count($x);
    $ys=array_sum($y)/count($y);    
    $a=0;$bx=0;$by=0;
    for($i=0;$i<count($x);$i++){     
        $xr=$x[$i]-$xs;
        $yr=$y[$i]-$ys;     
        $a+=$xr*$yr;        
        $bx+=pow($xr,2);
        $by+=pow($yr,2);
    }   
    $b = sqrt($bx*$by);
    if($b==0) return 0;
    return $a/$b;
}
function getAlphabet($urut = NULL){
    $arr = [];
    for($x = 'A'; $x < 'ZZ'; $x++){
        $arr[] = $x;
    }
    if(!is_null($urut)){
        return $arr[$urut];
    } else {
        return $arr;
    }
}
function jmlJawabanOpsi($huruf, $soal_ujian, $items, $index, $soal){
    $jumlah_soal = $soal_ujian->jumlah_soal;
    $benar = 0;
    foreach($items as $item){
        if(isset($item->anggota_rombel->jawaban_pd[$index])){
            if ($item->anggota_rombel->jawaban_pd[$index]->jawaban == $huruf){
                $benar++;
            }
        }
    }
    return ($benar) ? $benar / $jumlah_soal : 0;
}
function prop_corrent($count, $peserta){
    return ($count) ? $count / $peserta : 0;
}
function reratajmlJawabanOpsi($soal_ujian, $items, $soal, $index){
    $max_1 = [];
    $max_2 = [];
    for ($i = 0; $i < $soal_ujian->jumlah_pg; $i++){
        $max_2[] = ($soal->jawaban == getAlphabet($i)) ? jmlJawabanOpsi(getAlphabet($i), $soal_ujian, $soal, $index, $items) : '0';
        $max_1[] = jmlJawabanOpsi(getAlphabet($i), $soal_ujian, $items, $index, $soal);
        //dump($i);
    }
    //dd($jml);
    return [
        'max_1' => (array_sum($max_1)) ? max($max_1) : 0,
        'max_2' => (array_sum($max_2)) ? max($max_2) : 0,
    ];
    //return jmlJawabanOpsi(getAlphabet($i), $data->jadwal_ujian->soal_ujian, $soal, $index, $data->items);
}
function gantiformat($nomorhp) {
    //Terlebih dahulu kita trim dl
    $nomorhp = trim($nomorhp);
   //bersihkan dari karakter yang tidak perlu
    $nomorhp = strip_tags($nomorhp);     
   // Berishkan dari spasi
   $nomorhp= str_replace(" ","",$nomorhp);
   // bersihkan dari bentuk seperti  (022) 66677788
    $nomorhp= str_replace("(","",$nomorhp);
   // bersihkan dari format yang ada titik seperti 0811.222.333.4
    $nomorhp= str_replace(".","",$nomorhp); 

    //cek apakah mengandung karakter + dan 0-9
    if(!preg_match('/[^+0-9]/',trim($nomorhp))){
        // cek apakah no hp karakter 1-3 adalah +62
        if(substr(trim($nomorhp), 0, 3)=='62'){
            $nomorhp= trim($nomorhp);
        }
        // cek apakah no hp karakter 1 adalah 0
       elseif(substr($nomorhp, 0, 1)=='0'){
            $nomorhp= '62'.substr($nomorhp, 1);
        }
    }
    return $nomorhp;
}