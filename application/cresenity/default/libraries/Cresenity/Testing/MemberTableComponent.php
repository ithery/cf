<?php

namespace Cresenity\Testing;

class MemberTableComponent extends \CComponent {
    use \CComponent_Trait_WithPagination;

    public $perPage = 10;

    public $sortField = 'name';

    public $sortAsc = true;

    public $search = '';

    public $foo;

    protected $queryString = [
        'foo',
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function clear() {
        $this->search = '';
    }

    public function doRedirect() {
        //return \CF::redirect(\curl::base().'home/test');
        //\curl::redirect(\curl::base().'home/test');
        //$this->emit('alert', ['success', 'Record has been updated']);
        $this->redirectTo = \curl::base() . 'home';
    }

    public function doEvent() {
        //return \CF::redirect(\curl::base().'home/test');
        //\curl::redirect(\curl::base().'home/test');
        $this->emit('alert', ['success', 'Record has been updated']);
        //$this->redirectTo = \curl::base().'home';
    }

    public function sortBy($field) {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    public function mount() {
        $this->fill(\CHTTP::request()->only('search', 'page'));
    }

    public function render() {
        return \CView::factory('component.member-table', [
            'members' => MemberModel::search($this->search)
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->paginate($this->perPage)->setPath(\curl::base() . 'home/component'),
        ]);
    }
}
