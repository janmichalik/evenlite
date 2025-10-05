import { Injectable, Type } from '@angular/core';
import { BehaviorSubject } from 'rxjs';
import { Tab } from './tabs.model';

@Injectable({ providedIn: 'root' })

export class TabsService {
  private tabs: Tab[] = [];
  private activeTabId: string | null = null;

  tabs$ = new BehaviorSubject<Tab[]>([]);
  activeTab$ = new BehaviorSubject<Tab | null>(null);

  open(tab: Tab) {
    const existing = this.tabs.find(t => t.id === tab.id);

    if (existing) {
      this.activate(tab.id);
      return;
    }

    this.tabs.push(tab);
    this.tabs$.next(this.tabs);
    this.activate(tab.id);
  }

  activate(id: string) {
    const tab = this.tabs.find(t => t.id === id);
    if (tab) {
      this.activeTabId = id;
      this.activeTab$.next(tab);
    }
  }

  close(id: string) {
    this.tabs = this.tabs.filter(t => t.id !== id);
    this.tabs$.next(this.tabs);

    if (this.activeTabId === id) {
      const fallback = this.tabs[this.tabs.length - 1] || null;
      this.activeTabId = fallback?.id || null;
      this.activeTab$.next(fallback);
    }
  }

  getActive(): Tab | null {
    return this.tabs.find(t => t.id === this.activeTabId) || null;
  }
}
