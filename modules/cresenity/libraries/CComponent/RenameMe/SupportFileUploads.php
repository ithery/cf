<?php




class CComponent_RenameMe_SupportFileUploads
{
    static function init() { return new static; }

    function __construct()
    {
        CComponent_Manager::instance()->listen('property.hydrate', function ($property, $value, $component, $request) {
            $uses = array_flip(c::classUsesRecursive($component));

            if (! in_array(CComponent_Trait_WithFileUploads::class, $uses)) return;

            if (CComponent_TemporaryUploadedFile::canUnserialize($value)) {
                $component->{$property} = CComponent_TemporaryUploadedFile::unserializeFromComponentRequest($value);
            }
        });

        CComponent_Manager::instance()->listen('property.dehydrate', function ($property, $value, $component, $response) {
            $uses = array_flip(c::classUsesRecursive($component));

            if (! in_array(CComponent_Trait_WithFileUploads::class, $uses)) return;

            $newValue = $this->dehydratePropertyFromWithFileUploads($value);

            if ($newValue !== $value) {
                $component->{$property} = $newValue;
            }
        });
    }

    public function dehydratePropertyFromWithFileUploads($value)
    {
        if (CComponent_TemporaryUploadedFile::canUnserialize($value)) {
            return CComponent_TemporaryUploadedFile::unserializeFromComponentRequest($value);
        }

        if ($value instanceof CComponent_TemporaryUploadedFile) {
            return  $value->serializeForComponentResponse();
        }

        if (is_array($value) && isset(array_values($value)[0]) && array_values($value)[0] instanceof CComponent_TemporaryUploadedFile && is_numeric(key($value))) {
            $class = array_values($value)[0];
            return $class::serializeMultipleForLivewireResponse($value);
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->dehydratePropertyFromWithFileUploads($item);
            }
        }

        return $value;
    }
}
