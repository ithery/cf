<?php

namespace Cresenity\Testing;

class MemberTableComponent extends \CComponent {

    use \CComponent_Trait_WithPagination;
    public $perPage = 10;
    public $sortField = 'name';
    public $sortAsc = true;
    public $search = '';
    
    public function clear() {
        $this->search='';
    }
    
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    
    public function render() {
        return \CView::factory('component.member-table', [
                    'members' => MemberModel::search($this->search)
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->paginate($this->perPage)->setPath(\curl::base().'home/component'),
        ]);
    }

}
