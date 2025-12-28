import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import StatsCard from '../../src/components/StatsCard.vue'

describe('StatsCard', () => {
  it('renders value and label', () => {
    const wrapper = mount(StatsCard, {
      props: {
        icon: 'link',
        value: 42,
        label: 'Total Links',
      }
    })
    expect(wrapper.text()).toContain('42')
    expect(wrapper.text()).toContain('Total Links')
  })

  it('renders string value', () => {
    const wrapper = mount(StatsCard, {
      props: {
        icon: 'ctr',
        value: '85.5%',
        label: 'CTR',
      }
    })
    expect(wrapper.text()).toContain('85.5%')
  })

  it('renders link icon', () => {
    const wrapper = mount(StatsCard, {
      props: {
        icon: 'link',
        value: 10,
        label: 'Links',
      }
    })
    expect(wrapper.find('svg').exists()).toBe(true)
  })

  it('renders eye icon', () => {
    const wrapper = mount(StatsCard, {
      props: {
        icon: 'eye',
        value: 100,
        label: 'Views',
      }
    })
    expect(wrapper.find('svg').exists()).toBe(true)
  })

  it('renders click icon', () => {
    const wrapper = mount(StatsCard, {
      props: {
        icon: 'click',
        value: 50,
        label: 'Clicks',
      }
    })
    expect(wrapper.find('svg').exists()).toBe(true)
  })

  it('renders ctr icon', () => {
    const wrapper = mount(StatsCard, {
      props: {
        icon: 'ctr',
        value: '50%',
        label: 'CTR',
      }
    })
    expect(wrapper.find('svg').exists()).toBe(true)
  })

  it('handles zero value', () => {
    const wrapper = mount(StatsCard, {
      props: {
        icon: 'link',
        value: 0,
        label: 'Links',
      }
    })
    expect(wrapper.text()).toContain('0')
  })

  it('handles large numbers', () => {
    const wrapper = mount(StatsCard, {
      props: {
        icon: 'eye',
        value: 1000000,
        label: 'Views',
      }
    })
    expect(wrapper.text()).toContain('1000000')
  })
})
