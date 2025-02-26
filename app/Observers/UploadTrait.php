<?php

namespace App\Observers;

use Bkwld\Croppa\Facade as Croppa;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Image;

trait UploadTrait
{
    protected $dir = '';

    /**
     * @param string|UploadedFile|null $file
     *
     * @return string|null
     */
    protected function uploadFile($file)
    {
        if ($file instanceof UploadedFile) {
            if (! $file->isFile()) {
                return null;
            }
            $filename = $file->hashName();

            $website = \Hyn\Tenancy\Facades\TenancyFacade::website();
            if ($website) {
                Storage::disk('tenant')->putFileAs($this->dir, $file, $filename);
                $allowedMimeTypes = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
                $contentType = Storage::disk('tenant')->mimeType($this->dir.'/'.$filename);

                if (in_array($contentType, $allowedMimeTypes)) {
                    $path = Storage::disk('tenant')->path($this->dir.'/'.$filename);
                    $img = Image::make($path);
                    if ($img->height() > 800 || $img->width() > 800) {
                        $img->resize(800, 800, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $img->save();
                    }
                }
            } else {
                Storage::putFileAs('public/'.$this->dir, $file, $filename);
            }

            return $filename;
        }

        return $file;
    }

    /**
     * @param string|UploadedFile|null $file
     *
     * @return UploadObserver
     */
    protected function deleteFile($file): self
    {
        if (! $file) {
            return $this;
        }
        if ($file instanceof UploadedFile) {
            $file = $file->getFilename();
        }

        $website = null; //\Hyn\Tenancy\Facades\TenancyFacade::website();
        if ($website) {
            Storage::disk('tenant')->delete($this->dir.'/'.$file);
            //Croppa::delete(Croppa::url($this->dir.'/'.$file));
        } else {
            Storage::delete('public/'.$this->dir.'/'.$file);
        }

        return $this;
    }

    /**
     * @param string|UploadedFile|null $file
     *
     * @return string|null
     */
    protected function getMimeType($file)
    {
        if (! $file) {
            return null;
        }
        if ($file instanceof UploadedFile) {
            return $file->getMimeType();
        }

        $website = null; //\Hyn\Tenancy\Facades\TenancyFacade::website();
        if ($website) {
            return Storage::disk('tenant')->mimeType($this->dir.'/'.$file);
        } else {
            return Storage::mimeType('public/'.$this->dir.'/'.$file);
        }
    }
}
