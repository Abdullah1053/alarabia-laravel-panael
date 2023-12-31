<?php

namespace App\DataTables;
use App\Traits\DataTableTrait;

use App\Models\ProviderPayout;
use App\Models\Bank;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PayoutHistoryDataTable extends DataTable
{
    use DataTableTrait;
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->editColumn('payment_method', function($payout) {
                return !empty($payout->payment_method) ? $payout->payment_method : 'cash';
            })
            ->addColumn('bank_name', function($payout) {
               
            if($payout->payment_method == 'bank'){
                $bank = Bank::where('id',$payout->bank_id)->value('bank_name');
                return $bank;
            }
            else{
                return '-';
            }
            
            })
            ->editColumn('provider_id', function($payout) {
                return ($payout->providers != null && isset($payout->providers)) ? $payout->providers->display_name : '-';
            })
            ->editColumn('amount', function($payout) {
                return ($payout->amount != null && isset($payout->amount)) ? getPriceFormat($payout->amount) : '-';
            })
            ->editColumn('created_at', function($payout) {
                return $payout->created_at;
            })
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\ProviderPayout $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ProviderPayout $model)
    {
        return $model->where('provider_id',$this->id)->newQuery()->myPayout();
        
    }
    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('DT_RowIndex')
                ->searchable(false)
                ->title(__('messages.no'))
                ->orderable(false),
            Column::make('provider_id')
                    ->title(__('messages.provider')),
            Column::make('payment_method'),
            Column::make('bank_name')
                ->title(__('messages.bank_name')),
            Column::make('created_at')
                ->title(__('messages.paid_date')),
            Column::make('amount')
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Category_' . date('YmdHis');
    }
}
