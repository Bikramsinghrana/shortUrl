import './bootstrap';
import { initCommonUi } from './common.js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

initCommonUi();
