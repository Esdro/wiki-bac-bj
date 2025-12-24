import { Injectable } from '@angular/core';
import { toast } from 'ngx-sonner';
import { ToastOptions } from '../utils/constants';

@Injectable({
  providedIn: 'root',
})
export class NotifService {


  showToast(options: ToastOptions) {
    if (options.accent === 'success') {
      toast.success(options.title ?? "Succès", {
        description: options.description ?? 'Opération réussie.',
        action: {
          label: options.action?.label ?? 'Close',
          onClick: options.action?.onClick ?? (() => console.log('Close')),
        },
        position: options.position ?? 'top-right',
      });
      return;
    }
    
    if (options.accent === 'error') {
      toast.error(options.title ?? "Erreur", {
        description: options.description ?? 'Une erreur est survenue.',
        action: {
          label: options.action?.label ?? 'Close',
          onClick: options.action?.onClick ?? (() => console.log('Close')),
        },
        position: options.position ?? 'top-right',
      });
      return;
    }

    toast(options.title ?? "Oups", {
      description: options.description ?? 'Quelque chose s\'est mal passée.',
      action: {
        label: options.action?.label ?? 'Close',
        onClick: options.action?.onClick ?? (() => console.log('Close')),
      },
      position: options.position ?? 'top-right'
    });
  }

}
