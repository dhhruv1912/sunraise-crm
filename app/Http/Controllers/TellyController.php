<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Http;
use App\Models\Settings;
use Illuminate\Support\Facades\Log;

class TellyController extends Controller
{
    protected $tallyUrl;
    protected $ledger_enties;
    protected $CompanyName;
    protected $YearStart;
    protected $YearEnd;

    public function __construct()
    {
        $host = '127.0.0.1';
        $port = '9000';
        $this->tallyUrl = Settings::getValue('tally_tally_url','http://' . $host . ':' . $port);
        try {
            $response = Http::timeout(10)->get($this->tallyUrl);
            if ($response->successful()) {
                $connection = true;
            } else {
                $connection = false;
            }
        } catch (\Exception $e) {
            $connection = false;
        }
        // $ledger_enties = public_path('assets/admin/json/ledger_entries.json');
        // $this->ledger_enties = json_decode(file_get_contents($ledger_enties),true);
        $this->ledger_enties = [];

        if ($connection) {
            $CompanyName = Settings::getValue('tally_CompanyName');
            $YearStart = Settings::getValue('tally_YearStart');
            $YearEnd = Settings::getValue('tally_YearEnd');
            $this->CompanyName = $CompanyName;
            $this->YearStart = date("Ymd", strtotime($YearStart));
            $this->YearEnd = date("Ymd", strtotime($YearEnd));
        }
    }

    public function dashboard(Request $request){
        return view('page.tally.dashboard',compact('request'));
    }

    public function sendRequest($xml)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'text/xml',
            'ngrok-skip-browser-warning' => 'true',
            'User-Agent' => 'CustomUserAgent'
        ])->withBody($xml, 'text/xml')->post($this->tallyUrl);
        if (!$response->successful()) {
            Log::error('Ngrok request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);
        }
        if ($response->successful()) {
            $responseBody = $response->body();
            // $cleanXml = preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', "");
            $sanitizedXML = str_replace('&#4;','',preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $responseBody));
            return $sanitizedXML;
        }

        return null;
    }

    public function loadStockVouchers(Request $request)
    {
        $stock_name = urldecode($request->get('stock',''));
        $meta = [];
        $meta['CompanyName'] = $this->CompanyName;
        $meta['YearStart'] = $this->YearStart;
        $meta['YearEnd'] = $this->YearEnd;
        $meta['stock_name'] = $stock_name;

        $requestXML = view('page.tally.request.stock_voucher',compact('meta'))->render();
        // $requestXML2 = view('page.tally.request.stock_opening',compact('meta'))->render();
        $responseXML = $this->sendRequest($requestXML);
        $data = $this->xmlToArray(simplexml_load_string($responseXML));
        $vouchers = [];
        if(isset($data['DSPVCHDATE'])){
            if(is_array($data['DSPVCHDATE'])){
                foreach ($data['DSPVCHDATE'] as $key => $value) {
                    $vouchers[] = [
                        'date' => $value,
                        'account' => $data['DSPVCHITEMACCOUNT'][$key],
                        'type' => $data['DSPVCHTYPE'][$key],
                        'in' => [
                            'quentity' =>  gettype($data['DSPINBLOCK'][$key]['DSPVCHINQTY']) == 'string' ? $data['DSPINBLOCK'][$key]['DSPVCHINQTY'] : "",
                            'amount' =>  gettype($data['DSPINBLOCK'][$key]['DSPVCHINAMT']) == 'string' ? $data['DSPINBLOCK'][$key]['DSPVCHINAMT'] : "",
                        ],
                        'out' => [
                            'quentity' =>  gettype($data['DSPOUTBLOCK'][$key]['DSPVCHOUTQTY']) == 'string' ? $data['DSPOUTBLOCK'][$key]['DSPVCHOUTQTY'] : "",
                            'amount' =>  gettype($data['DSPOUTBLOCK'][$key]['DSPVCHNETTOUTAMT']) == 'string' ? $data['DSPOUTBLOCK'][$key]['DSPVCHNETTOUTAMT'] : "",
                        ],
                        'closing' => [
                            'quentity' =>  gettype($data['DSPCLBLOCK'][$key]['DSPVCHCLQTY']) == 'string' ? $data['DSPCLBLOCK'][$key]['DSPVCHCLQTY'] : "",
                            'amount' =>  gettype($data['DSPCLBLOCK'][$key]['DSPVCHCLAMT']) == 'string' ? $data['DSPCLBLOCK'][$key]['DSPVCHCLAMT'] : "",
                        ],

                    ];
                }
            }else if($data['DSPVCHDATE'] != ""){
                $vouchers[] = [
                    'date' => $data['DSPVCHDATE'],
                    'account' => $data['DSPVCHITEMACCOUNT'],
                    'type' => $data['DSPVCHTYPE'],
                    'in' => [
                        'quentity' =>  gettype($data['DSPINBLOCK']['DSPVCHINQTY']) == 'string' ? $data['DSPINBLOCK']['DSPVCHINQTY'] : "",
                        'amount' =>  gettype($data['DSPINBLOCK']['DSPVCHINAMT']) == 'string' ? $data['DSPINBLOCK']['DSPVCHINAMT'] : "",
                    ],
                    'out' => [
                        'quentity' =>  gettype($data['DSPOUTBLOCK']['DSPVCHOUTQTY']) == 'string' ? $data['DSPOUTBLOCK']['DSPVCHOUTQTY'] : "",
                        'amount' =>  gettype($data['DSPOUTBLOCK']['DSPVCHNETTOUTAMT']) == 'string' ? $data['DSPOUTBLOCK']['DSPVCHNETTOUTAMT'] : "",
                    ],
                    'closing' => [
                        'quentity' =>  gettype($data['DSPCLBLOCK']['DSPVCHCLQTY']) == 'string' ? $data['DSPCLBLOCK']['DSPVCHCLQTY'] : "",
                        'amount' =>  gettype($data['DSPCLBLOCK']['DSPVCHCLAMT']) == 'string' ? $data['DSPCLBLOCK']['DSPVCHCLAMT'] : "",
                    ],
                ];
            }
        }
        return response()->json([
            'data' => $vouchers,
            'status' => 200,
        ],200);
    }
    public function loadLedgerVouchers(Request $request)
    {
        // set php time out to max and memory to mex
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '2048M');
        $ledger_name = urldecode($request->get('ledger',''));
        $meta = [];
        $meta['CompanyName'] = $this->CompanyName;
        $meta['YearStart'] = $this->YearStart;
        $meta['YearEnd'] = $this->YearEnd;
        $requestXML = view('page.tally.request.ledger_voucher',compact('ledger_name','meta'))->render();
        $requestXML2 = view('page.tally.request.voucher_pdf',compact('ledger_name','meta'))->render();
        // dd($requestXML);
        $responseXML = $this->sendRequest($requestXML);
        $metadata = $this->sendRequest($requestXML2);
        $metadata = $this->xmlToArray(simplexml_load_string($metadata));
        $data = $this->xmlToArray(simplexml_load_string($responseXML));
        $vouchers = [];
        $balance = [];
        $parent = '';
        if(isset($metadata['BODY']['DATA']['TALLYMESSAGE']['LEDGER'])){
            $parent = (string) gettype( $metadata['BODY']['DATA']['TALLYMESSAGE']['LEDGER']['PARENT']) == 'string' ? $metadata['BODY']['DATA']['TALLYMESSAGE']['LEDGER']['PARENT'] : '';
            $balance['closing'] = (string) gettype( $metadata['BODY']['DATA']['TALLYMESSAGE']['LEDGER']['CLOSINGBALANCE']) == 'string' ? $metadata['BODY']['DATA']['TALLYMESSAGE']['LEDGER']['CLOSINGBALANCE'] : '0.00';
            $balance['opening'] = (string) gettype( $metadata['BODY']['DATA']['TALLYMESSAGE']['LEDGER']['OPENINGBALANCE']) == 'string' ? $metadata['BODY']['DATA']['TALLYMESSAGE']['LEDGER']['OPENINGBALANCE'] : '0.00';
        }
        if(isset($data['DSPVCHDATE'])){
            if(is_array($data['DSPVCHDATE'])){
                foreach ($data['DSPVCHDATE'] as $key => $value) {
                    $vouchers[] = [
                        'date' => $value,
                        'account' => $data['DSPVCHLEDACCOUNT'][$key],
                        'type' => $data['DSPVCHTYPE'][$key],
                        'credit' => $data['DSPVCHCRAMT'][$key] ?? 0,
                        'debit' => $data['DSPVCHDRAMT'][$key] ?? 0,

                    ];
                }
            }else if($data['DSPVCHDATE'] != ""){
                $vouchers[] = [
                    'date' => $data['DSPVCHDATE'],
                    'account' => $data['DSPVCHLEDACCOUNT'],
                    'type' => $data['DSPVCHTYPE'],
                    'credit' => $data['DSPVCHCRAMT'],
                    'debit' => $data['DSPVCHDRAMT'],
                ];
            }
        }
        return response()->json([
            'data' => $vouchers,
            'balance' => $balance,
            'status' => 200,
            'ledger_enties' => $this->ledger_enties[$parent] ?? 'credit'
        ],200);
    }

    public function ledger(Request $request){
        return view('page.tally.ledger',compact('request'));
    }
    public function stocks(Request $request){
        return view('page.tally.stocks',compact('request'));
    }
    public function loadLedger(Request $request){
        $meta = [];
        $meta['CompanyName'] = $this->CompanyName;
        $meta['YearStart'] = $this->YearStart;
        $meta['YearEnd'] = $this->YearEnd;
        $requestXML = view('page.tally.request.ledger',compact('meta'))->render();
        $data = $this->sendRequest($requestXML);
        $voucher_type_mapping = json_decode(Settings::getValue('tally_voucher_type_mapping'),true);
        return response()->json([
            'data' => simplexml_load_string($data),
            'voucher_type_mapping' => $voucher_type_mapping,
            'status' => 200,
        ],200);
    }
    public function loadStocks(Request $request){
        $meta = [];
        $meta['CompanyName'] = $this->CompanyName;
        $meta['YearStart'] = $this->YearStart;
        $meta['YearEnd'] = $this->YearEnd;
        $requestXML = view('page.tally.request.stocks',compact('meta'))->render();
        $data = $this->sendRequest($requestXML);
        return response()->json([
            'data' => simplexml_load_string($data),
            'status' => 200,
        ],200);
    }
    public function test(Request $request){
        $meta = [];
        $meta['CompanyName'] = $this->CompanyName;
        $meta['YearStart'] = $this->YearStart;
        $meta['YearEnd'] = $this->YearEnd;
        $requestXML = view('page.tally.request.test',compact('meta'))->render();
        // dd($requestXML);
        $data = $this->sendRequest($requestXML);
        return response()->json([
            'data' => simplexml_load_string($data),
            'status' => 200,
        ],200);
    }

    public function xmlToArray($xml) {
        return json_decode(json_encode($xml), true);
    }
}

// http://127.0.0.1:5500/SRI/tally/add
