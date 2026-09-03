/**
 * CAMPUS Mail Engine — Sequence Engine (Drip Campaigns)
 * 
 * TRIGGER → FILTER → SEGMENT → ACTION → WAIT → CONDITION → NEXT ACTION
 */

import { v4 as uuidv4 } from 'uuid';

export class SequenceEngine {
  constructor() {
    this.sequences = new Map();
  }

  create(data) {
    const sequence = {
      id: uuidv4(),
      name: data.name,
      trigger: data.trigger, // e.g., 'newsletter_signup', 'double_opt_in_confirmed'
      steps: data.steps || [],
      active: data.active ?? false,
      created_at: new Date().toISOString(),
    };
    this.sequences.set(sequence.id, sequence);
    return sequence;
  }

  get(id) { return this.sequences.get(id) || null; }
  getAll() { return Array.from(this.sequences.values()); }
  getByTrigger(trigger) { return this.getAll().filter(s => s.trigger === trigger && s.active); }

  /**
   * Get next step for a contact in a sequence
   */
  getNextStep(sequenceId, currentStepIndex) {
    const seq = this.get(sequenceId);
    if (!seq || currentStepIndex >= seq.steps.length - 1) return null;
    return seq.steps[currentStepIndex + 1];
  }
}

/**
 * Marketing Automation — Trigger/Action processor
 */
export class AutomationEngine {
  constructor() {
    this.rules = [];
  }

  addRule(rule) {
    this.rules.push({
      id: uuidv4(),
      trigger: rule.trigger,
      conditions: rule.conditions || [],
      actions: rule.actions || [],
      active: rule.active ?? true,
    });
  }

  /**
   * Process an event and return actions to execute
   */
  processEvent(event) {
    const matchingRules = this.rules.filter(r =>
      r.active && r.trigger === event.type
    );

    const actions = [];
    for (const rule of matchingRules) {
      // Check conditions
      const conditionsMet = rule.conditions.every(cond =>
        this._evaluateCondition(cond, event)
      );

      if (conditionsMet) {
        actions.push(...rule.actions.map(a => ({
          ...a,
          rule_id: rule.id,
          triggered_by: event.type,
          contact_id: event.entity_id,
        })));
      }
    }

    return actions;
  }

  _evaluateCondition(condition, event) {
    const { field, operator, value } = condition;
    const actual = event.metadata?.[field];

    switch (operator) {
      case 'equals': return actual === value;
      case 'not_equals': return actual !== value;
      case 'contains': return String(actual).includes(value);
      case 'greater_than': return Number(actual) > Number(value);
      case 'less_than': return Number(actual) < Number(value);
      case 'exists': return actual !== undefined && actual !== null;
      default: return false;
    }
  }
}
