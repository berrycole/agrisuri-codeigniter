<?php
namespace App\Controllers;

class Simulation extends BaseController
{
    private bool $friendlyMode=false;
    private function path(): string { return WRITEPATH . ($this->friendlyMode || $this->request->getPost('workspace')==='friendly' ? 'agrisuri-friendly.json' : 'agrisuri.json'); }
    private function seed(): array { return [
        'plans'=>[['id'=>'H-001','coop'=>'Cooperative A','qty'=>1000,'date'=>date('Y-m-d',strtotime('+7 days'))]],
        'batches'=>[['id'=>'R-001','plan'=>'H-001','coop'=>'Cooperative A','received'=>900,'cleared'=>800,'held'=>100,'packaged'=>400,'reserved'=>0,'cost'=>0]],
        'requests'=>[['id'=>'B-001','buyer'=>'Sample buyer','qty'=>600,'deadline'=>date('Y-m-d',strtotime('+5 days')),'packaging'=>'Packaged','location'=>'Cagayan de Oro','reserved'=>0,'sent'=>0,'accepted'=>0,'rejected'=>0,'price'=>0,'payment'=>'Unpaid']],
        'cases'=>[['id'=>'C-001','request'=>'B-001','batch'=>'R-001','issue'=>'200 kg packaging gap','evidence'=>'R-001: 800 kg cleared, 400 kg packaged; B-001 needs 600 kg packaged.','action'=>'','owner'=>'','due'=>'','estimate'=>0,'actual'=>0,'outcome'=>'']],
        'events'=>[['time'=>date('Y-m-d H:i'),'text'=>'Demonstration case loaded.']]
    ]; }
    private function load(): array { if (!is_file($this->path())) $this->save($this->seed()); return json_decode(file_get_contents($this->path()),true) ?: $this->seed(); }
    private function save(array $d): void { file_put_contents($this->path(),json_encode($d,JSON_PRETTY_PRINT),LOCK_EX); }
    private function idx(array $rows,string $id): ?int { foreach($rows as $i=>$r) if($r['id']===$id) return $i; return null; }
    public function index() {
        $html=file_get_contents(ROOTPATH.'docs/index.html');
        $bootstrap='<script>window.AGRISURI_BOOTSTRAP='.json_encode($this->load(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT).';</script>';
        return str_replace(['<!--BOOTSTRAP-->','href="assets/','src="assets/'],[$bootstrap,'href="/assets/','src="/assets/'],$html);
    }
    public function friendly() {
        $this->friendlyMode=true;
        $html=file_get_contents(ROOTPATH.'docs/friendly/index.html');
        $bootstrap='<script>window.AGRISURI_BOOTSTRAP='.json_encode($this->load(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT).';</script>';
        return str_replace(['<!--BOOTSTRAP-->','href="../assets/','src="../assets/'],[$bootstrap,'href="/assets/','src="/assets/'],$html);
    }
    public function action() {
        $d=$this->load(); $p=$this->request->getPost(); $type=$p['type']??''; $tab=$p['tab']??'overview'; $q=fn($k)=>max(0,(float)($p[$k]??0)); $msg='Record saved.'; $error=null;
        try {
        foreach(['qty','cleared','held','cost','estimate','actual','sent','accepted','rejected','price'] as $key) {
            if(isset($p[$key]) && (!is_numeric($p[$key]) || !is_finite((float)$p[$key]) || (float)$p[$key]<0)) throw new \RuntimeException('Quantities and costs must be valid nonnegative numbers.');
        }
        switch($type) {
            case 'plan':
                if(empty($p['coop'])||empty($p['date'])||$q('qty')<=0) throw new \RuntimeException('Cooperative, date and positive quantity are required.');
                $id='H-'.str_pad(count($d['plans'])+1,3,'0',STR_PAD_LEFT); $d['plans'][]=['id'=>$id,'coop'=>trim($p['coop']),'qty'=>$q('qty'),'date'=>$p['date']]; $msg="$id added as expected supply; it cannot be reserved."; break;
            case 'batch':
                $i=$this->idx($d['plans'],$p['plan']??''); if($i===null||$q('qty')<=0) throw new \RuntimeException('Choose a plan and enter actual quantity.');
                $id='R-'.str_pad(count($d['batches'])+1,3,'0',STR_PAD_LEFT); $d['batches'][]=['id'=>$id,'plan'=>$p['plan'],'coop'=>$d['plans'][$i]['coop'],'received'=>$q('qty'),'cleared'=>0,'held'=>$q('qty'),'packaged'=>0,'reserved'=>0,'cost'=>0]; $msg="$id created. Stock awaits inspection."; break;
            case 'inspect':
                $i=$this->idx($d['batches'],$p['batch']??''); if($i===null) throw new \RuntimeException('Choose a batch.'); $b=&$d['batches'][$i];
                if(abs($q('cleared')+$q('held')-$b['received'])>0.001||$q('cleared')<$b['packaged']||$q('cleared')<$b['reserved']) throw new \RuntimeException('Cleared plus held must equal received quantity and preserve packaged and reserved stock.');
                $b['cleared']=$q('cleared'); $b['held']=$q('held'); $msg='Inspection saved. Held quantity cannot be offered.'; break;
            case 'package':
                $i=$this->idx($d['batches'],$p['batch']??''); if($i===null) throw new \RuntimeException('Choose a batch.'); $b=&$d['batches'][$i];
                if($q('qty')<=0||$b['packaged']+$q('qty')>$b['cleared']) throw new \RuntimeException('Packaging needs cleared, unpackaged stock.');
                $b['packaged']+=$q('qty'); $b['cost']+=$q('cost'); $msg=$q('qty').' kg packaged and cost recorded.'; break;
            case 'request':
                if(empty($p['buyer'])||empty($p['deadline'])||$q('qty')<=0) throw new \RuntimeException('Buyer, quantity and deadline are required.');
                $id='B-'.str_pad(count($d['requests'])+1,3,'0',STR_PAD_LEFT); $d['requests'][]=['id'=>$id,'buyer'=>trim($p['buyer']),'qty'=>$q('qty'),'deadline'=>$p['deadline'],'packaging'=>$p['packaging']??'Packaged','location'=>trim($p['location']??''),'reserved'=>0,'sent'=>0,'accepted'=>0,'rejected'=>0,'price'=>0,'payment'=>'Unpaid']; $msg="$id entered. Review matches before reserving."; break;
            case 'reserve':
                $bi=$this->idx($d['batches'],$p['batch']??''); $ri=$this->idx($d['requests'],$p['request']??''); if($bi===null||$ri===null||$q('qty')<=0) throw new \RuntimeException('Choose batch, request and quantity.');
                $b=&$d['batches'][$bi]; $r=&$d['requests'][$ri]; $eligible=$r['packaging']==='Packaged'?$b['packaged']:$b['cleared'];
                if($q('qty')>$eligible-$b['reserved']||$q('qty')>$r['qty']-$r['reserved']) throw new \RuntimeException('Only eligible, unreserved stock within the request balance can be reserved.');
                $b['reserved']+=$q('qty'); $r['reserved']+=$q('qty'); $d['allocations'][]=['batch'=>$b['id'],'request'=>$r['id'],'qty'=>$q('qty')]; $msg=$q('qty')." kg reserved from {$b['id']} for {$r['id']}."; break;
            case 'handling':
                $i=$this->idx($d['batches'],$p['batch']??''); if($i===null||empty($p['location'])||empty($p['notes'])) throw new \RuntimeException('Select a batch and enter the location and observed handling event.');
                $d['batches'][$i]['location']=trim($p['location']); $d['batches'][$i]['cost']+=$q('cost'); $msg=$p['batch'].' handling at '.trim($p['location']).': '.trim($p['notes']).' · Recorded cost '.$q('cost').' PHP'; break;
            case 'case':
                if($this->idx($d['batches'],$p['batch']??'')===null||$this->idx($d['requests'],$p['request']??'')===null||empty($p['issue'])) throw new \RuntimeException('Link a batch and request and describe an observed issue.');
                $id='C-'.str_pad(count($d['cases'])+1,3,'0',STR_PAD_LEFT); $d['cases'][]=['id'=>$id,'request'=>$p['request'],'batch'=>$p['batch'],'issue'=>trim($p['issue']),'evidence'=>trim($p['evidence']??''),'action'=>'','owner'=>'','due'=>'','estimate'=>0,'actual'=>0,'outcome'=>'']; $msg="$id opened; staff should investigate the cause."; break;
            case 'approve':
                $i=$this->idx($d['cases'],$p['case']??''); if($i===null||empty($p['action'])||empty($p['owner'])||empty($p['due'])) throw new \RuntimeException('Action, owner and due date are required.'); $c=&$d['cases'][$i]; $c['action']=$p['action']; $c['owner']=trim($p['owner']); $c['due']=$p['due']; $c['estimate']=$q('estimate'); $msg="{$c['id']} action approved and assigned."; break;
            case 'outcome':
                $i=$this->idx($d['cases'],$p['case']??''); if($i===null||empty($p['outcome'])) throw new \RuntimeException('Describe the observed outcome.'); $c=&$d['cases'][$i]; if(empty($c['action'])) throw new \RuntimeException('Approve and assign an intervention before recording its outcome.'); $c['outcome']=trim($p['outcome']); $c['actual']=$q('actual'); $msg='Outcome and actual cost recorded.'; break;
            case 'delivery':
                $i=$this->idx($d['requests'],$p['request']??''); if($i===null) throw new \RuntimeException('Choose a request.'); $r=&$d['requests'][$i];
                if($q('sent')<=0||$q('sent')>$r['reserved']||abs($q('accepted')+$q('rejected')-$q('sent'))>0.001) throw new \RuntimeException('Dispatch needs reserved stock; accepted plus rejected must account for the dispatched quantity.');
                $r['sent']=$q('sent'); $r['accepted']=$q('accepted'); $r['rejected']=$q('rejected'); $r['price']=$q('price'); $r['payment']=$p['payment']??'Unpaid'; $msg='Buyer feedback recorded. Investigate any rejection.'; break;
            case 'reset': $d=$this->seed(); $msg='Demonstration reset.'; break;
            default: throw new \RuntimeException('Unknown action.');
        } $d['events'][]=['time'=>date('Y-m-d H:i'),'text'=>$msg]; $this->save($d);
        } catch(\RuntimeException $e) { $error=$e->getMessage(); }
        if(($p['ajax']??'')==='1') return $this->response->setStatusCode($error?422:200)->setJSON(['error'=>$error,'message'=>$msg,'data'=>$this->load()]);
        session()->setFlashdata($error?'error':'message',$error?:$msg); return redirect()->to('/?tab='.rawurlencode($tab));
    }
}
