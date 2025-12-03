<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Project;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function global(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (! $q) {
            return response()->json([]);
        }

        // Customers
        $customers = Customer::where('name', 'like', "%{$q}%")
            ->orWhere('mobile', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->limit(10)
            ->get()
            ->map(fn ($c) => [
                'type' => 'customer',
                'id' => $c->id,
                'label' => "{$c->name} ({$c->mobile})",
                'data' => $c,
            ]);

        // Leads
        $leads = Lead::where('lead_code', 'like', "%{$q}%")
            ->orWhere('remarks', 'like', "%{$q}%")
            ->limit(10)
            ->get()
            ->map(fn ($l) => [
                'type' => 'lead',
                'id' => $l->id,
                'label' => "{$l->lead_code}",
                'data' => $l,
            ]);

        // Projects
        $projects = Project::where('project_code', 'like', "%{$q}%")
            ->orWhere('customer_name', 'like', "%{$q}%")
            ->orWhere('mobile', 'like', "%{$q}%")
            ->limit(10)
            ->get()
            ->map(fn ($p) => [
                'type' => 'project',
                'id' => $p->id,
                'label' => "{$p->project_code} - {$p->customer_name}",
                'data' => $p,
            ]);

        return response()->json([
            'customers' => $customers->values(),
            'leads' => $leads->values(),
            'projects' => $projects->values(),
        ]);
    }

    public function search(Request $request)
    {
        $q = trim($request->query('q', ''));

        // Normalized versions
        $qNormalized = strtolower($q);
        $qDigits = preg_replace('/\D/', '', $q);

        $limit = 7;

        // -----------------------------
        // CUSTOMERS
        // -----------------------------
        $customers = \App\Models\Customer::query()
            ->when($q, function ($sql) use ($q, $qDigits) {
                $sql->where(function ($s) use ($q, $qDigits) {
                    $s->where('name', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%")
                        ->orWhere('mobile', 'like', "%$qDigits%")
                        ->orWhere('alternate_mobile', 'like', "%$qDigits%");
                });
            })
            ->limit($limit)
            ->get()
            ->map(function ($c) use ($q) {
                return [
                    'id' => $c->id,
                    'type' => 'customer',
                    'label' => $this->highlight($c->name, $q),
                    'sub' => $c->mobile,
                    'extra' => $c->email,
                    'badge' => 'Customer',
                ];
            });

        // -----------------------------
        // LEADS
        // -----------------------------
        $leads = \App\Models\Lead::query()
            ->when($q, function ($sql) use ($q, $qDigits) {
                $sql->where('lead_code', 'like', "%$q%")
                    ->orWhereHas('customer', function ($c) use ($q, $qDigits) {
                        $c->where('name', 'like', "%$q%")
                            ->orWhere('mobile', 'like', "%$qDigits%");
                    });
            })
            ->limit($limit)
            ->get()
            ->map(function ($l) use ($q) {
                return [
                    'id' => $l->id,
                    'type' => 'lead',
                    'label' => $this->highlight($l->lead_code, $q),
                    'sub' => @$l->customer->name ?? '—',
                    'extra' => @$l->customer->mobile ?? '',
                    'badge' => strtoupper($l->status),
                ];
            });

        // -----------------------------
        // PROJECTS
        // -----------------------------
        $projects = \App\Models\Project::query()
            ->when($q, function ($sql) use ($q) {
                $sql->where('project_code', 'like', "%$q%")
                    ->orWhere('customer_name', 'like', "%$q%");
            })
            ->limit($limit)
            ->get()
            ->map(function ($p) use ($q) {
                return [
                    'id' => $p->id,
                    'type' => 'project',
                    'label' => $this->highlight($p->project_code, $q),
                    'sub' => $p->customer_name,
                    'extra' => $p->mobile,
                    'badge' => ucfirst($p->status),
                ];
            });

        return response()->json([
            'results' => [
                'customers' => $customers->values(),
                'leads' => $leads->values(),
                'projects' => $projects->values(),
            ],
        ]);
    }

    private function highlight($text, $needle)
    {
        if (!$needle) return $text;
        return str_ireplace($needle, "<mark>$needle</mark>", $text);
    }
}
