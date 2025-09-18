import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {
    static values = { mercureUrl: String }
    async initialize() {
        this.component = await getComponent(this.element);
    }    
    
    connect() {     
        this.eventSource = new EventSource(this.mercureUrlValue);
        
        this.eventSource.onmessage = event => {
            const data = JSON.parse(event.data);
            this.component.action('start', data);
        }
    }

}
