import { TestBed } from '@angular/core/testing';

import { Dark } from './dark';

describe('Dark', () => {
  let service: Dark;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(Dark);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
