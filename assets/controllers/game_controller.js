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
                        
            if(data.score) {
                this.component.action('otherScoreUp', data);
            }
        }
    }

    moveLeft()
    {
        this.component.action('moveLeft');
    }

    moveRight()
    {
        this.component.action('moveRight');
    }

    moveDown()
    {
        this.component.action('moveDown');
    }

    rotateLeft()
    {
        this.component.action('rotateLeft');
    }

    rotateRight()
    {
        this.component.action('rotateRight');
    }
}
