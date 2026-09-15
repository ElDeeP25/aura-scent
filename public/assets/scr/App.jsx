import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { ShoppingBag, Sparkles, Home as HomeIcon, Compass, User, ArrowRight, X, Plus, Minus, CheckCircle } from 'lucide-react';

export default function App() {
  const [currentView, setCurrentView] = useState('home');
  const [cart, setCart] = useState([
    { id: 1, name: 'Royal Amber Elixir', price: 145.0, qty: 1, image: 'https://images.unsplash.com/photo-1523293182086-7651a899d37f?auto=format&fit=crop&q=80&w=800' },
    { id: 2, name: 'Midnight Velvet Oud', price: 180.0, qty: 2, image: 'https://images.unsplash.com/photo-1594035910387-fea47794261f?auto=format&fit=crop&q=80&w=800' }
  ]);

  const products = [
    { id: 1, name: 'Royal Amber Elixir', price: 145.0, category: 'Unisex', description: 'Warm amber, golden spices, and rich vanilla.', image: 'https://images.unsplash.com/photo-1523293182086-7651a899d37f?auto=format&fit=crop&q=80&w=800' },
    { id: 2, name: 'Midnight Velvet Oud', price: 180.0, category: 'Men', description: 'Smoky oud, dark rose, and mysterious leather.', image: 'https://images.unsplash.com/photo-1594035910387-fea47794261f?auto=format&fit=crop&q=80&w=800' },
    { id: 3, name: 'Celestial Blossom', price: 120.0, category: 'Women', description: 'White jasmine, sparkling bergamot, and white musk.', image: 'https://images.unsplash.com/photo-1588405748880-12d1d2a59f75?auto=format&fit=crop&q=80&w=800' },
    { id: 4, name: 'Golden Tobacco Scent', price: 165.0, category: 'Unisex', description: 'Rich tobacco leaves laced with honey and warm woods.', image: 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&q=80&w=800' }
  ];

  const addToCart = (product) => {
    setCart(prev => {
      const existing = prev.find(item => item.id === product.id);
      if (existing) {
        return prev.map(item => item.id === product.id ? { ...item, qty: item.qty + 1 } : item);
      }
      return [...prev, { ...product, qty: 1 }];
    });
  };

  return (
    <div className="bg-[#050505] text-white min-h-screen font-sans selection:bg-[#d4af37] selection:text-black">
      {/* Navigation Bar */}
      <header className="fixed top-0 left-0 w-full z-50 bg-[#050505]/85 backdrop-blur-xl border-b border-[#d4af37]/15">
        <div className="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
          <span className="text-xl font-extrabold tracking-[0.25em] bg-gradient-to-r from-white via-neutral-200 to-[#d4af37] bg-clip-text text-transparent cursor-pointer" onClick={() => setCurrentView('home')}>
            AURA & SCENT
          </span>

          <nav className="hidden md:flex items-center gap-8 text-xs font-bold tracking-[0.2em] uppercase text-neutral-400">
            <button onClick={() => setCurrentView('home')} className={`transition-colors hover:text-[#d4af37] ${currentView === 'home' ? 'text-[#d4af37]' : ''}`}>Home</button>
            <button onClick={() => setCurrentView('shop')} className={`transition-colors hover:text-[#d4af37] ${currentView === 'shop' ? 'text-[#d4af37]' : ''}`}>Shop</button>
            <button onClick={() => setCurrentView('ai')} className={`flex items-center gap-2 px-4 py-2 rounded-full border border-[#d4af37]/40 bg-[#d4af37]/10 text-[#f3e5ab] hover:bg-[#d4af37]/20 transition-all ${currentView === 'ai' ? 'border-[#d4af37]' : ''}`}>
              <Sparkles className="w-4 h-4 text-[#d4af37]" /> AI Advisor
            </button>
            <button onClick={() => setCurrentView('cart')} className={`relative transition-colors hover:text-[#d4af37] flex items-center gap-1 ${currentView === 'cart' ? 'text-[#d4af37]' : ''}`}>
              <ShoppingBag className="w-4 h-4" /> Cart ({cart.reduce((a, c) => a + c.qty, 0)})
            </button>
          </nav>
        </div>
      </header>

      {/* Main Container Views */}
      <main className="pt-20">
        <AnimatePresence mode="wait">
          {currentView === 'home' && (
            <motion.section key="home" initial={{ opacity: 0, y: 15 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -15 }} transition={{ duration: 0.4 }} className="relative h-[90vh] flex items-center justify-center text-center px-4 bg-[url('https://images.unsplash.com/photo-1615397349754-cfa2066a298e?auto=format&fit=crop&q=80&w=1920')] bg-cover bg-center">
              <div className="absolute inset-0 bg-gradient-to-t from-[#050505] via-[#050505]/70 to-transparent"></div>
              <div className="relative z-10 max-w-3xl">
                <span className="text-xs font-bold uppercase tracking-[0.3em] text-[#d4af37] mb-4 block">Artisanal Haute Parfumerie</span>
                <h1 className="text-5xl md:text-7xl font-black uppercase tracking-wider mb-6 leading-tight">Redefining Luxury Through Scent</h1>
                <p className="text-neutral-400 text-sm md:text-base tracking-wide mb-10 max-w-xl mx-auto leading-relaxed">Immerse your senses in rare, handcrafted fragrances designed to capture moments of pure elegance.</p>
                <div className="flex gap-4 justify-center">
                  <button onClick={() => setCurrentView('shop')} className="px-8 py-4 rounded-full bg-[#d4af37] text-black font-extrabold text-xs uppercase tracking-[0.2em] hover:bg-transparent hover:text-[#f3e5ab] border border-[#d4af37] transition-all">Explore Collection</button>
                  <button onClick={() => setCurrentView('ai')} className="px-8 py-4 rounded-full bg-transparent text-white font-extrabold text-xs uppercase tracking-[0.2em] border border-neutral-700 hover:border-[#d4af37] hover:text-[#d4af37] transition-all flex items-center gap-2">
                    <Sparkles className="w-4 h-4 text-[#d4af37]" /> Ask AI Expert
                  </button>
                </div>
              </div>
            </motion.section>
          )}

          {currentView === 'shop' && (
            <motion.section key="shop" initial={{ opacity: 0, y: 15 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -15 }} transition={{ duration: 0.4 }} className="max-w-7xl mx-auto px-6 py-16">
              <h2 className="text-3xl font-black uppercase tracking-wider mb-10 text-center">Exquisite Collections</h2>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                {products.map(product => (
                  <div key={product.id} className="bg-[#0e0e0e] border border-[#d4af37]/20 rounded-2xl overflow-hidden group hover:border-[#d4af37] transition-all duration-300 flex flex-col justify-between">
                    <div>
                      <div className="h-64 overflow-hidden relative">
                        <img src={product.image} alt={product.name} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                        <span className="absolute top-3 left-3 bg-black/70 backdrop-blur-md px-3 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase text-[#d4af37] border border-[#d4af37]/30">{product.category}</span>
                      </div>
                      <div className="p-6">
                        <h3 className="font-bold text-lg mb-2">{product.name}</h3>
                        <p className="text-neutral-400 text-xs line-clamp-2 leading-relaxed mb-4">{product.description}</p>
                        <div className="text-[#f3e5ab] font-extrabold text-base">${product.price.toFixed(2)}</div>
                      </div>
                    </div>
                    <div className="p-6 pt-0">
                      <button onClick={() => addToCart(product)} className="w-full py-3 rounded-xl border border-[#d4af37] text-[#d4af37] hover:bg-[#d4af37] hover:text-black font-extrabold text-xs uppercase tracking-widest transition-all">
                        Add to Cart
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            </motion.section>
          )}

          {currentView === 'ai' && (
            <motion.section key="ai" initial={{ opacity: 0, y: 15 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -15 }} transition={{ duration: 0.4 }} className="max-w-3xl mx-auto px-6 py-16">
              <div className="bg-[#0e0e0e] border border-[#d4af37]/30 rounded-3xl p-8 shadow-2xl">
                <div className="text-center mb-8">
                  <div className="inline-flex p-3 rounded-2xl bg-[#d4af37]/10 border border-[#d4af37]/30 text-[#d4af37] mb-4">
                    <Sparkles className="w-6 h-6" />
                  </div>
                  <h2 className="text-2xl font-black uppercase tracking-wider mb-2">AI Scent Matchmaker</h2>
                  <p className="text-neutral-400 text-xs tracking-wide">Describe your aura or preferred notes (e.g., warm amber, smoky oud, sweet vanilla), and our intelligence will curate your signature match.</p>
                </div>
                <div className="bg-black border border-neutral-800 rounded-2xl p-4 h-64 overflow-y-auto mb-6 flex flex-col gap-3">
                  <div className="bg-neutral-900 border border-neutral-800 p-3 rounded-xl text-xs max-w-[80%] text-neutral-300">
                    Hello! I am your personal luxury fragrance concierge. What scent profile are you drawn to today?
                  </div>
                </div>
                <div className="flex gap-3">
                  <input type="text" placeholder="Type your preference (e.g., rich vanilla)..." className="flex-1 bg-black border border-neutral-800 px-5 py-4 rounded-full text-xs text-white focus:border-[#d4af37] outline-none transition-colors" />
                  <button className="bg-[#d4af37] text-black px-6 rounded-full font-extrabold text-xs uppercase tracking-widest hover:bg-[#f3e5ab] transition-colors">Send</button>
                </div>
              </div>
            </motion.section>
          )}

          {currentView === 'cart' && (
            <motion.section key="cart" initial={{ opacity: 0, y: 15 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -15 }} transition={{ duration: 0.4 }} className="max-w-5xl mx-auto px-6 py-16">
              <h2 className="text-3xl font-black uppercase tracking-wider mb-10">Shopping Bag</h2>
              {cart.length === 0 ? (
                <div className="text-center py-20 text-neutral-500">Your bag is empty. <button onClick={() => setCurrentView('shop')} className="text-[#d4af37] underline ml-2">Explore Shop</button></div>
              ) : (
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                  <div className="lg:col-span-2 flex flex-col gap-4">
                    {cart.map(item => (
                      <div key={item.id} className="bg-[#0e0e0e] border border-neutral-800 p-4 rounded-2xl flex items-center gap-4">
                        <img src={item.image} alt={item.name} className="w-20 h-20 object-cover rounded-xl" />
                        <div className="flex-1">
                          <h4 className="font-bold text-sm mb-1">{item.name}</h4>
                          <div className="text-[#f3e5ab] text-xs font-bold">${item.price.toFixed(2)}</div>
                          <div className="text-neutral-500 text-[11px] mt-1">Quantity: {item.qty}</div>
                        </div>
                      </div>
                    ))}
                  </div>
                  <div className="bg-[#0e0e0e] border border-[#d4af37]/30 rounded-2xl p-6 h-fit">
                    <h3 className="font-bold text-sm uppercase tracking-wider mb-6 pb-3 border-b border-neutral-800">Order Summary</h3>
                    <div className="flex justify-between text-xs text-neutral-400 mb-4"><span>Subtotal</span><span>${cart.reduce((acc, item) => acc + (item.price * item.qty), 0).toFixed(2)}</span></div>
                    <div className="flex justify-between text-xs text-neutral-400 mb-6"><span>Shipping</span><span>$15.00</span></div>
                    <div className="flex justify-between text-sm font-extrabold text-white pt-4 border-t border-neutral-800 mb-6"><span>Total</span><span className="text-[#f3e5ab]">${(cart.reduce((acc, item) => acc + (item.price * item.qty), 0) + 15).toFixed(2)}</span></div>
                    <button className="w-full bg-[#d4af37] text-black py-4 rounded-full font-extrabold text-xs uppercase tracking-widest hover:bg-white transition-colors">Proceed to Checkout</button>
                  </div>
                </div>
              )}
            </motion.section>
          )}
        </AnimatePresence>
      </main>
    </div>
  );
}