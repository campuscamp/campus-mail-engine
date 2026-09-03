/**
 * CAMPUS Mail Engine — Segments Manager
 */
import { readFileSync } from 'fs';

export class SegmentManager {
  constructor(configPath) {
    this.segments = new Map();
    if (configPath) this.loadFromFile(configPath);
  }

  loadFromFile(path) {
    const data = JSON.parse(readFileSync(path, 'utf-8'));
    for (const seg of data.segments || data) {
      this.segments.set(seg.id, seg);
    }
  }

  get(id) { return this.segments.get(id) || null; }
  getAll() { return Array.from(this.segments.values()); }
  exists(id) { return this.segments.has(id); }

  getIds() { return Array.from(this.segments.keys()); }
}
