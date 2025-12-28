import { describe, it, expect, vi, beforeEach } from 'vitest'
import axios from 'axios'

// Mock axios
vi.mock('axios', async () => {
  return {
    default: {
      create: vi.fn(() => ({
        get: vi.fn(),
        post: vi.fn(),
        put: vi.fn(),
        delete: vi.fn(),
        interceptors: {
          request: { use: vi.fn() },
          response: { use: vi.fn() },
        },
      })),
    },
  }
})

describe('API Service', () => {
  beforeEach(() => {
    localStorage.clear()
    vi.resetModules()
  })

  describe('axios instance creation', () => {
    it('creates axios instance with correct base configuration', async () => {
      await import('../../src/services/api.js')

      expect(axios.create).toHaveBeenCalledWith({
        baseURL: expect.any(String),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      })
    })
  })

  describe('localStorage token handling', () => {
    it('returns null when no token exists', () => {
      expect(localStorage.getItem('token')).toBeNull()
    })

    it('stores and retrieves token correctly', () => {
      localStorage.setItem('token', 'test-token')
      expect(localStorage.getItem('token')).toBe('test-token')
    })

    it('removes token correctly', () => {
      localStorage.setItem('token', 'test-token')
      expect(localStorage.getItem('token')).toBe('test-token')
      localStorage.removeItem('token')
      expect(localStorage.getItem('token')).toBeNull()
    })
  })

  describe('user data handling', () => {
    it('stores and retrieves user data correctly', () => {
      const user = { id: 1, name: 'Test', email: 'test@example.com' }
      localStorage.setItem('user', JSON.stringify(user))
      expect(JSON.parse(localStorage.getItem('user'))).toEqual(user)
    })

    it('returns null for non-existent user', () => {
      expect(localStorage.getItem('user')).toBeNull()
    })
  })
})

describe('API Functions Structure', () => {
  beforeEach(() => {
    vi.resetModules()
  })

  it('exports all required auth functions', async () => {
    const api = await import('../../src/services/api.js')

    expect(typeof api.register).toBe('function')
    expect(typeof api.login).toBe('function')
    expect(typeof api.logout).toBe('function')
    expect(typeof api.getUser).toBe('function')
  })

  it('exports all required links functions', async () => {
    const api = await import('../../src/services/api.js')

    expect(typeof api.getLinks).toBe('function')
    expect(typeof api.createLink).toBe('function')
    expect(typeof api.getLink).toBe('function')
    expect(typeof api.updateLink).toBe('function')
    expect(typeof api.deleteLink).toBe('function')
  })

  it('exports all required trash functions', async () => {
    const api = await import('../../src/services/api.js')

    expect(typeof api.getTrashedLinks).toBe('function')
    expect(typeof api.restoreLink).toBe('function')
    expect(typeof api.forceDeleteLink).toBe('function')
  })

  it('exports all required stats functions', async () => {
    const api = await import('../../src/services/api.js')

    expect(typeof api.getStats).toBe('function')
    expect(typeof api.getDetailedStats).toBe('function')
  })
})
