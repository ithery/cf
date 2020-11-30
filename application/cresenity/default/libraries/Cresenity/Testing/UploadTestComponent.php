<?php

namespace Cresenity\Testing;

class UploadTestComponent extends \CComponent {

    use \CComponent_Trait_WithFileUploads;

    public $photo;

    public function mount() {
        
    }
    
    public function save()
    {
        $this->validate([
            'photo' => 'image|max:1024', // 1MB Max
        ]);
        $this->photo->store('photos');
    }

    public function render() {
        return \CView::factory('component.test.upload');
    }

}
